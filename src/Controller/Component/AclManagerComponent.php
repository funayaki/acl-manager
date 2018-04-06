<?php
use AclManager\Controller\Component\AclReflectorComponent;

/**
 * @property AclReflectorComponent $AclReflector
 */
namespace AclManager\Controller\Component;

use Acl\Controller\Component\AclComponent;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * @property AclReflectorComponent AclReflector
 * @property AclComponent Acl
 */
class AclManagerComponent extends Component
{
    var $components = array('Auth', 'Acl.Acl', 'AclManager.AclReflector', 'Session');

    /**
     * @var null
     */
    private $controller = null;

    /**
     * @var
     */
    private $controllers_hash_file;

    /****************************************************************************************/

    public function initialize(array $config)
    {
        $this->controllers_hash_file = CACHE . 'persistent' . DS . 'controllers_hashes.txt';
    }

    /****************************************************************************************/

    /**
     * Check if the file containing the stored controllers hashes can be created,
     * and create it if it does not exist
     *
     * @return boolean true if the file exists or could be created
     */
    private function checkControllerHashTmpFile()
    {
        if (is_writable(dirname($this->controllers_hash_file))) {
            $file = new File($this->controllers_hash_file, true);
            return $file->exists();
        } else {
            $this->Session->setFlash(sprintf(__d('acl', 'the {0} directory is not writable'), dirname($this->controllers_hash_file)), 'flash_error', null, 'plugin_acl');
            return false;
        }
    }

    /****************************************************************************************/

    public function checkUserModelActsAsAclRequester($model_classname)
    {
//		if(!isset($this->controller->{$model_classname}))
//		{
//			/*
//			 * Do not use $this->controller->loadModel, as calling it from a plugin may prevent correct loading of behaviors
//			 */
//			$user_model = ClassRegistry :: init($model_classname);
//		}
//		else
//		{
//			$user_model = $this->controller->{$model_classname};
//		}

        $user_model = $this->getModelInstance($model_classname);

        $behaviors = $user_model->behaviors();
        if (!empty($behaviors) && $behaviors->has('Acl')) {
            $type = $behaviors->get('Acl')->config('type');
            return $type == 'requester';
        }

        return false;
    }

    /**
     * Check if a given field_expression is an existing fieldname for the given model
     *
     * If it doesn't exist, a virtual field called 'alaxos_acl_display_name' is created with the given expression
     *
     * @param string $model_classname
     * @param string $field_expression
     * @return string The name of the field to use as display name
     */
    public function setDisplayName($model_classname, $field_expression)
    {
        $model_instance = $this->getModelInstance($model_classname);

        $schema = $model_instance->schema();

        if ($schema->hasColumn($field_expression)
            ||
            $schema->hasColumn(str_replace($model_classname . '.', '', $field_expression))
        ) {
            /*
             * The field does not need to be created as it already exists in the model
             * as a datatable field, or a virtual field configured in the model
             */

            /*
             * Eventually remove the model name
             */
            if (strpos($field_expression, $model_classname . '.') === 0) {
                $field_expression = str_replace($model_classname . '.', '', $field_expression);
            }

            return $field_expression;
        } else {
            /*
             * The field does not exist in the model
             * -> create a virtual field with the given expression
             */

            $this->controller->{$model_classname}->virtualFields['alaxos_acl_display_name'] = $field_expression;

            return 'alaxos_acl_display_name';
        }
    }

    /**
     * Return an instance of the given model name
     *
     * @param string $model_classname
     * @return \Cake\ORM\Table
     */
    private function getModelInstance($model_classname)
    {
        if (!isset($this->controller->{$model_classname})) {
            /*
             * Do not use $this->controller->loadModel, as calling it from a plugin may prevent correct loading of behaviors
             */
            $model_instance = TableRegistry::get($model_classname);
        } else {
            $model_instance = $this->controller->{$model_classname};
        }

        return $model_instance;
    }

    /**
     * return the stored array of controllers hashes
     *
     * @return array
     */
    public function getStoredControllersHashes()
    {
        if ($this->checkControllerHashTmpFile()) {
            $file = new File($this->controllers_hash_file);
            $file_content = $file->read();

            if (!empty($file_content)) {
                $stored_controller_hashes = unserialize($file_content);
            } else {
                $stored_controller_hashes = array();
            }

            return $stored_controller_hashes;
        }
    }

    /**
     * return an array of all controllers hashes
     *
     * @return array
     */
    public function getCurrentControllersHashes()
    {
        $controllers = $this->AclReflector->getAllControllers();

        $current_controller_hashes = array();

        foreach ($controllers as $path => $controller) {
            $ctler_file = new File($path);
            $current_controller_hashes[$path] = $ctler_file->md5();
        }

        return $current_controller_hashes;
    }

    /**
     * Return ACOs paths that should exist in the ACO datatable but do not exist
     */
    function getMissingACOs()
    {
        $actions = $this->AclReflector->getAllActions();
        $existingActions = $this->getActionsInDatabase();
        return array_diff($actions, $existingActions);
    }

    /**
     * Store missing ACOs for all actions in the datasource
     * If necessary, it creates actions parent nodes (plugin and controller) as well
     */
    public function createACOs()
    {
        $Aco = $this->Acl->Aco;

        $missing_acos = $this->getMissingACOs();

        $rootNode = $this->getRootNode();

        $log = array();
        foreach ($missing_acos as $missing_aco) {
            $aco_path_parts = explode('/', $missing_aco);

            $path = '';
            $parent_node = $rootNode;

            foreach ($aco_path_parts as $aco_path_part) {
                $path .= '/' . $aco_path_part;

                $look_path = substr($path, 1);

                /*
                 * Check if the ACO exists
                 */
                $query = $Aco->node($look_path);

                if (empty($query)) {
                    $parent_id = null;

                    if (isset($parent_node)) {
                        $parent_id = $parent_node->id;
                    }

                    $alias = substr($path, strrpos($path, '/') + 1);

                    $new_node = $Aco->newEntity(['parent_id' => $parent_id, 'model' => null, 'alias' => $alias]);
                    if ($Aco->save($new_node)) {
                        $log[] = sprintf(__d('acl', "Aco node '%s' created"), $look_path);

                        /*
                         * The newly created ACO node is the parent of the next ones to create (if there are some left to create)
                         */
                        $parent_node = $new_node;
                    }
                } else {
                    $parent_node = $query->first();
                }
            }
        }

        return $log;
    }

    public function updateControllersHashFile()
    {
        $current_controller_hashes = $this->getCurrentControllersHashes();

        $file = new File($this->controllers_hash_file);
        $file->write(serialize($current_controller_hashes));
    }

    public function controllerHashFileIsOutOfSync()
    {
        if ($this->checkControllerHashTmpFile()) {
            $stored_controller_hashes = $this->getStoredControllersHashes();
            $current_controller_hashes = $this->getCurrentControllersHashes();

            /*
             * Check what controllers have changed
             */
            $updated_controllers = array_keys(Hash:: diff($current_controller_hashes, $stored_controller_hashes));

            return !empty($updated_controllers);
        }
    }

    /**
     * @return array
     */
    public function getACOsToPrune()
    {
        $actions = $this->AclReflector->getAllActions();
        $existingActions = $this->getActionsInDatabase();
        return array_diff($existingActions, $actions);
    }

    /**
     * Remove all ACOs that don't have any corresponding controllers or actions.
     *
     * @return array log of removed ACO nodes
     */
    public function pruneACOs()
    {
        $Aco = $this->Acl->Aco;

        $log = [];

        $paths_to_prune = $this->getACOsToPrune();

        foreach ($paths_to_prune as $path_to_prune) {
            $query = $Aco->node($path_to_prune);
            if (!empty($query)) {
                $node = $query->first();
                $entity = $Aco->get($node->id);
                if ($Aco->delete($entity)) {
                    $log[] = sprintf(__d('acl', "Aco node '%s' has been deleted"), $path_to_prune);
                } else {
                    $log[] = '<span class="error">' . sprintf(__d('acl', "Aco node '%s' could not be deleted"), $path_to_prune) . '</span>';
                }
            }
        }

        return $log;
    }

    /**
     *
     * @param AclNode $aro_nodes The Aro model hierarchy
     * @param string $aco_path The Aco path to check for
     * @param string $permission_type 'deny' or 'allow', 'grant', depending on what permission (grant or deny) is being set
     */
    public function savePermission($aro_nodes, $aco_path, $permission_type)
    {
        if (isset($aro_nodes[0])) {
            $aco_path = $this->AclReflector->getRootNodeName() . '/' . $aco_path;

            $pk_name = 'id';
            if ($aro_nodes[0]['Aro']['model'] == Configure:: read('acl.aro.role.model')) {
                $pk_name = $this->getRolePrimaryKeyName();
            } elseif ($aro_nodes[0]['Aro']['model'] == Configure:: read('acl.aro.user.model')) {
                $pk_name = $this->getUserPrimaryKeyName();
            }

            $aro_model_data = array($aro_nodes[0]['Aro']['model'] => array($pk_name => $aro_nodes[0]['Aro']['foreign_key']));
            $aro_id = $aro_nodes[0]['Aro']['id'];

            $specific_permission_right = $this->getSpecificPermissionRight($aro_nodes[0], $aco_path);
            $inherited_permission_right = $this->getFirstParentPermissionRight($aro_nodes[0], $aco_path);

            if (!isset($inherited_permission_right) && count($aro_nodes) > 1) {
                /*
                 * Get the permission inherited by the parent ARO
                 */
                $specific_parent_aro_permission_right = $this->getSpecificPermissionRight($aro_nodes[1], $aco_path);

                if (isset($specific_parent_aro_permission_right)) {
                    /*
                     * If there is a specific permission for the parent ARO on the ACO, the child ARO inheritates this permission
                     */
                    $inherited_permission_right = $specific_parent_aro_permission_right;
                } else {
                    $inherited_permission_right = $this->getFirstParentPermissionRight($aro_nodes[1], $aco_path);
                }
            }

            /*
             * Check if the specific permission is necessary to get the correct permission
             */
            if (!isset($inherited_permission_right)) {
                $specific_permission_needed = true;
            } else {
                if ($permission_type == 'allow' || $permission_type == 'grant') {
                    $specific_permission_needed = ($inherited_permission_right != 1);
                } else {
                    $specific_permission_needed = ($inherited_permission_right == 1);
                }
            }

            if ($specific_permission_needed) {
                if ($permission_type == 'allow' || $permission_type == 'grant') {
                    if ($this->Acl->allow($aro_model_data, $aco_path)) {
                        return true;
                    } else {
                        trigger_error(__d('acl', 'An error occured while saving the specific permission'), E_USER_NOTICE);
                        return false;
                    }
                } else {
                    if ($this->Acl->deny($aro_model_data, $aco_path)) {
                        return true;
                    } else {
                        trigger_error(__d('acl', 'An error occured while saving the specific permission'), E_USER_NOTICE);
                        return false;
                    }
                }
            } elseif (isset($specific_permission_right)) {
                $aco_node = $this->Acl->Aco->node($aco_path);
                if (!empty($aco_node)) {
                    $aco_id = $aco_node[0]['Aco']['id'];

                    $specific_permission = $this->Acl->Aro->Permission->find('first', array('conditions' => array('aro_id' => $aro_id, 'aco_id' => $aco_id)));

                    if (!empty($specific_permission)) {
                        if ($this->Acl->Aro->Permission->delete(array('Permission.id' => $specific_permission['Permission']['id']))) {
                            return true;
                        } else {
                            trigger_error(__d('acl', 'An error occured while deleting the specific permission'), E_USER_NOTICE);
                            return false;
                        }
                    } else {
                        /*
                         * As $specific_permission_right has a value, we should never fall here, but who knows... ;-)
                         */

                        trigger_error(__d('acl', 'The specific permission id could not be retrieved'), E_USER_NOTICE);
                        return false;
                    }
                } else {
                    /*
                     * As $specific_permission_right has a value, we should never fall here, but who knows... ;-)
                     */
                    trigger_error(__d('acl', 'The child ACO id could not be retrieved'), E_USER_NOTICE);
                    return false;
                }
            } else {
                /*
                 * Right can be inherited, and no specific permission exists => there is nothing to do...
                 */
            }
        } else {
            trigger_error(__d('acl', 'Invalid ARO'), E_USER_NOTICE);
            return false;
        }
    }

    private function getSpecificPermissionRight($aro_node, $aco_path)
    {
        $pk_name = 'id';
        if ($aro_node['Aro']['model'] == Configure:: read('acl.aro.role.model')) {
            $pk_name = $this->getRolePrimaryKeyName();
        } elseif ($aro_node['Aro']['model'] == Configure:: read('acl.aro.user.model')) {
            $pk_name = $this->getUserPrimaryKeyName();
        }

        $aro_model_data = array($aro_node['Aro']['model'] => array($pk_name => $aro_node['Aro']['foreign_key']));
        $aro_id = $aro_node['Aro']['id'];

        /*
         * Check if a specific permission of the ARO's on the ACO already exists in the datasource
         * =>
         * 		1) the ACO node must exist in the ACO table
         * 		2) a record with the aro_id and aco_id must exist in the aros_acos table
         */
        $aco_id = null;
        $specific_permission = null;
        $specific_permission_right = null;

        $aco_node = $this->Acl->Aco->node($aco_path);
        if (!empty($aco_node)) {
            $aco_id = $aco_node[0]['Aco']['id'];

            $specific_permission = $this->Acl->Aro->Permission->find('first', array('conditions' => array('aro_id' => $aro_id, 'aco_id' => $aco_id)));

            if (!empty($specific_permission)) {
                /*
                 * Check the right (grant => true / deny => false) of this specific permission
                 */
                $specific_permission_right = $this->Acl->check($aro_model_data, $aco_path);

                if ($specific_permission_right) {
                    return 1;    // allowed
                } else {
                    return -1;    // denied
                }
            }
        }

        return null; // no specific permission found
    }

    private function getFirstParentPermissionRight($aro_node, $aco_path)
    {
        $pk_name = 'id';
        if ($aro_node['Aro']['model'] == Configure:: read('acl.aro.role.model')) {
            $pk_name = $this->getRolePrimaryKeyName();
        } elseif ($aro_node['Aro']['model'] == Configure:: read('acl.aro.user.model')) {
            $pk_name = $this->getUserPrimaryKeyName();
        }

        $aro_model_data = array($aro_node['Aro']['model'] => array($pk_name => $aro_node['Aro']['foreign_key']));
        $aro_id = $aro_node['Aro']['id'];

        while (strpos($aco_path, '/') !== false && !isset($parent_permission_right)) {
            $aco_path = substr($aco_path, 0, strrpos($aco_path, '/'));

            $parent_aco_node = $this->Acl->Aco->node($aco_path);
            if (!empty($parent_aco_node)) {
                $parent_aco_id = $parent_aco_node[0]['Aco']['id'];

                $parent_permission = $this->Acl->Aro->Permission->find('first', array('conditions' => array('aro_id' => $aro_id, 'aco_id' => $parent_aco_id)));

                if (!empty($parent_permission)) {
                    /*
                     * Check the right (grant => true / deny => false) of this first parent permission
                     */
                    $parent_permission_right = $this->Acl->check($aro_model_data, $aco_path);

                    if ($parent_permission_right) {
                        return 1;    // allowed
                    } else {
                        return -1;    // denied
                    }
                }
            }
        }

        return null; // no parent permission found
    }

    /**
     * Set the permissions of the authenticated user in Session
     * The session permissions are then used for instance by the AclHtmlHelper->link() function
     */
    public function setSessionPermissions()
    {
        if (!$this->Session->check('Alaxos.Acl.permissions')) {
            $actions = $this->AclReflector->getAllActions();

            $user = $this->Auth->user();

            if (!empty($user)) {
                $user = array(Configure:: read('acl.aro.user.model') => $user);
                $permissions = array();

                foreach ($actions as $action) {
                    $aco_path = $this->AclReflector->getRootNodeName() . '/' . $action;

                    $permissions[$aco_path] = $this->Acl->check($user, $aco_path);
                }

                $this->Session->write('Alaxos.Acl.permissions', $permissions);
            }
        }
    }

    /**
     * @return array
     */
    public function getActionsInDatabase()
    {
        $Aco = $this->Acl->Aco;

        $rootNode = $this->getRootNode();

        $acos = $Aco->find('children', ['for' => $rootNode->id, 'order' => 'lft'])->find('threaded');

        return array_map(function ($path) {
            return ltrim($path, '/');
        }, $this->_buildActionListInDatabase($acos));
    }

    /**
     * @param $acos
     * @param string $path
     * @return array
     */
    protected function _buildActionListInDatabase($acos, $path = '')
    {
        $actions = [];
        foreach ($acos as $aco) {
            $currentPath = $path . '/' . $aco->alias;
            if ($aco->children) {
                $childActions = $this->_buildActionListInDatabase($aco->children, $currentPath);
                foreach ($childActions as $childAction) {
                    $actions[] = $childAction;
                }
            } else {
                if ($this->_isActionNode($aco)) {
                    $actions[] = $currentPath;
                }
            }
        }
        return $actions;
    }

    /**
     * @param $node
     * @return bool
     */
    protected function _isActionNode($node)
    {
        return !($node->children || Inflector::camelize($node->alias) == $node->alias);
    }

    /**
     * Check root node for existence, create it if it doesn't exist.
     *
     * @return mixed
     */
    public function getRootNode()
    {
        $Aco = $this->Acl->Aco;

        $query = $Aco->node($this->AclReflector->getRootNodeName());
        if (!$query) {
            $rootNode = $Aco->newEntity(['parent_id' => null, 'model' => null, 'alias' => $this->AclReflector->getRootNodeName()]);
            $Aco->save($rootNode);
        } else {
            $rootNode = $query->first();
        }

        return $rootNode;
    }

    public function getRolePrimaryKeyName()
    {
        $forced_pk_name = Configure:: read('acl.aro.role.primary_key');
        if (!empty($forced_pk_name)) {
            return $forced_pk_name;
        } else {
            /*
             * Return the primary key's name that follows the CakePHP conventions
             */
            return 'id';
        }
    }

    public function getUserPrimaryKeyName()
    {
        $forced_pk_name = Configure:: read('acl.aro.user.primary_key');
        if (!empty($forced_pk_name)) {
            return $forced_pk_name;
        } else {
            /*
             * Return the primary key's name that follows the CakePHP conventions
             */
            return 'id';
        }
    }

    public function getRoleForeignKeyName()
    {
        $forced_fk_name = Configure:: read('acl.aro.role.foreign_key');
        if (!empty($forced_fk_name)) {
            return $forced_fk_name;
        } else {
            /*
             * Return the foreign key's name that follows the CakePHP conventions
             */
            return Inflector:: underscore(Inflector::singularize(Configure:: read('acl.aro.role.model'))) . '_id';
        }
    }
}