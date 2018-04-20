<?php
namespace AclManager\Controller\Admin;

use Acl\Adapter\DbAcl;
use Acl\Controller\Component\AclComponent;
use Acl\Model\Table\ArosTable;
use AclManager\Controller\AppController;
use AclManager\Controller\Component\AclReflectorComponent;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 *
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 *
 * @property AclReflectorComponent $AclReflector
 * @property ArosTable Aros
 * @property AclComponent Acl
 */
class ArosController extends AppController
{
    var $helpers = [
        'CakeJs.JqueryEngine',
        'CakeJs.Js' => ['Jquery']
    ];

    var $paginate = array(
        'limit' => 20,
        //'order' => array('display_name' => 'asc')
    );

    public function beforeFilter(Event $event)
    {
        $this->loadModel(Configure:: read('acl.aro.role.model'));
        $this->loadModel(Configure:: read('acl.aro.user.model'));

        parent:: beforeFilter($event);
    }

    public function index()
    {

    }

    public function check($run = null)
    {
        $user_model_name = Configure:: read('acl.aro.user.model');
        $role_model_name = Configure:: read('acl.aro.role.model');

        $user_display_field = $this->AclManager->setDisplayName($user_model_name, Configure:: read('acl.user.display_name'));
        $role_display_field = $this->AclManager->setDisplayName($role_model_name, Configure:: read('acl.aro.role.display_field'));

        $this->set('user_display_field', $user_display_field);
        $this->set('role_display_field', $role_display_field);

        $roles = $this->{$role_model_name}->find('all', array('order' => $role_display_field, 'contain' => false, 'recursive' => -1));

        $missing_aros = array('roles' => array(), 'users' => array());

        foreach ($roles as $role) {
            /*
             * Check if ARO for role exist
             */
            $aro = $this->Acl->Aro->find()
                ->where([
                    'model' => $role_model_name,
                    'foreign_key' => $role->{$this->AclManager->getRolePrimaryKeyName()}
                ])
                ->first();

            if (!$aro) {
                $missing_aros['roles'][] = $role;
            }
        }

        $users = $this->{$user_model_name}->find('all', array('order' => $user_display_field, 'contain' => false, 'recursive' => -1));
        foreach ($users as $user) {
            /*
             * Check if ARO for user exist
             */
            $aro = $this->Acl->Aro->find()
                ->where([
                    'model' => $user_model_name,
                    'foreign_key' => $user->{$this->AclManager->getUserPrimaryKeyName()}
                ])
                ->first();

            if (!$aro) {
                $missing_aros['users'][] = $user;
            }
        }


        if (isset($run)) {
            $this->set('run', true);

            /*
             * Complete roles AROs
             */
            if (count($missing_aros['roles']) > 0) {
                foreach ($missing_aros['roles'] as $k => $role) {
                    $aro = $this->Acl->Aro->newEntity([
                        'parent_id' => null,
                        'model' => $role_model_name,
                        'foreign_key' => $role->{$this->AclManager->getRolePrimaryKeyName()},
                    ]);

                    if ($this->Acl->Aro->save($aro)) {
                        unset($missing_aros['roles'][$k]);
                    }
                }
            }

            /*
             * Complete users AROs
             */
            if (count($missing_aros['users']) > 0) {
                foreach ($missing_aros['users'] as $k => $user) {
                    /*
                     * Find ARO parent for user ARO
                     */
                    $parent = $this->Acl->Aro->find()
                        ->where([
                            'model' => $role_model_name,
                            'foreign_key' => $user->{$this->AclManager->getRoleForeignKeyName()}
                        ])
                        ->first();

                    if ($parent) {
                        $aro = $this->Acl->Aro->newEntity(
                            ['parent_id' => $parent->id,
                                'model' => $user_model_name,
                                'foreign_key' => $user->{$this->AclManager->getUserPrimaryKeyName()},
                            ]);

                        if ($this->Acl->Aro->save($aro)) {
                            unset($missing_aros['users'][$k]);
                        }
                    }
                }
            }
        } else {
            $this->set('run', false);
        }

        $this->set('missing_aros', $missing_aros);

    }

    public function users()
    {
        $user_model_name = Configure:: read('acl.aro.user.model');
        $role_model_name = Configure:: read('acl.aro.role.model');

        $user_display_field = $this->AclManager->setDisplayName($user_model_name, Configure:: read('acl.user.display_name'));
        $role_display_field = $this->AclManager->setDisplayName($role_model_name, Configure:: read('acl.aro.role.display_field'));

        $this->paginate['order'] = array($user_display_field => 'asc');

        $this->set('user_display_field', $user_display_field);
        $this->set('role_display_field', $role_display_field);

        $this->{$role_model_name}->recursive = -1;
        $roles = $this->{$role_model_name}->find('all', array('order' => $role_display_field, 'contain' => false, 'recursive' => -1));

        $this->{$user_model_name}->recursive = -1;

        $user_display_value = $this->getRequest()->getData('User.' . $user_display_field);

        if (isset($user_display_value) || $this->getRequest()->getSession()->check('acl.aros.users.filter')) {
            if (!isset($user_display_value)) {
                $this->getRequest()->setData('User.' . $user_display_field, $this->getRequest()->getSession()->read('acl.aros.users.filter'));
            } else {
                $this->getRequest()->getSession()->write('acl.aros.users.filter', $this->getRequest()->getData('User.' . $user_display_field));
            }

            $filter = array($user_model_name . '.' . $user_display_field . ' LIKE' => '%' . $this->getRequest()->getData('User.' . $user_display_field) . '%');
        } else {
            $filter = array();
        }

        $this->{$user_model_name}->hasOne('Aros', [
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'Aros.model' => $user_model_name,
            ]
        ]);

        $this->paginate = [
            'contain' => ['Aros']
        ];
        $users = $this->paginate($this->{$user_model_name}, $filter);

        $missing_aro = (bool)array_filter($users->toArray(), function ($users) {
            return !$users['aro'];
        });

        $this->set('roles', $roles);
        $this->set('users', $users);
        $this->set('missing_aro', $missing_aro);
    }

    public function updateUserRole($user_pk, $role_pk)
    {
        $user_model_name = Configure:: read('acl.aro.user.model');

        $user = $this->{$user_model_name}->get($user_pk);
        $user->{$this->AclManager->getRoleForeignKeyName()} = $role_pk;

        if ($this->{$user_model_name}->save($user)) {
            $this->Flash->success(__d('acl', 'The user role has been updated'));
        } else {
            $this->Flash->error(print_r($user->getErrors(), true));
        }

        $this->_returnToReferer();
    }

    public function ajaxRolePermissions()
    {
        $role_model_name = Configure:: read('acl.aro.role.model');

        $role_display_field = $this->AclManager->setDisplayName($role_model_name, Configure:: read('acl.aro.role.display_field'));

        $this->set('role_display_field', $role_display_field);

        $this->{$role_model_name}->recursive = -1;
        $roles = $this->{$role_model_name}->find('all', array('order' => $role_display_field, 'contain' => false, 'recursive' => -1));

        $actions = $this->AclReflector->getAllActions();

        $this->set('roles', $roles);
        $this->set('actions', $actions);
    }

    public function rolePermissions()
    {
        $role_model_name = Configure:: read('acl.aro.role.model');

        $role_display_field = $this->AclManager->setDisplayName($role_model_name, Configure:: read('acl.aro.role.display_field'));

        $this->set('role_display_field', $role_display_field);

        $this->{$role_model_name}->recursive = -1;
        $roles = $this->{$role_model_name}->find('all', array('order' => $role_display_field, 'contain' => false, 'recursive' => -1));

        $actions = $this->AclReflector->getAllActions();

        $permissions = [];
        foreach ($actions as $full_action) {
            foreach ($roles as $role) {
                $aro_node = $this->Acl->Aro->node($role);
                if ($aro_node) {
                    $aco_node = $this->Acl->Aco->node($this->AclReflector->getRootNodeName() . '/' . $full_action);
                    if ($aco_node) {
                        $authorized = $this->Acl->check($role, $this->AclReflector->getRootNodeName() . '/' . $full_action);

                        $permissions[$full_action][$role->{$this->AclManager->getRolePrimaryKeyName()}] = $authorized ? 1 : 0;
                    }
                } else {
                    /*
                     * No check could be done as the ARO is missing
                     */
                    $permissions[$full_action][$role->{$this->AclManager->getRolePrimaryKeyName()}] = -1;
                }
            }
        }

        $this->set('roles', $roles);
        $this->set('actions', $actions);
        $this->set('permissions', $permissions);
    }

    public function userPermissions($user_id = null, $ajax = false)
    {
        $user_model_name = Configure:: read('acl.aro.user.model');
        $role_model_name = Configure:: read('acl.aro.role.model');

        $user_display_field = $this->AclManager->setDisplayName($user_model_name, Configure:: read('acl.user.display_name'));

        $this->paginate['order'] = array($user_display_field => 'asc');
        $this->set('user_display_field', $user_display_field);

        if (!$user_id) {
			$user_display_value = $this->getRequest()->getData('User.' . $user_display_field);
            if (isset($user_display_value) || $this->getRequest()->getSession()->check('acl.aros.user_permissions.filter')) {
                if (!isset($user_display_value)) {
					$this->getRequest()->setData('User.' . $user_display_field, $this->getRequest()->getSession()->read('acl.aros.user_permissions.filter'));
                } else {
                    $this->getRequest()->getSession()->write('acl.aros.user_permissions.filter', $this->getRequest()->getData('User.' . $user_display_field));
                }

                $filter = array($user_model_name . '.' . $user_display_field . ' LIKE' => '%' . $this->getRequest()->getData('User.' . $user_display_field) . '%');
            } else {
                $filter = array();
            }

            $users = $this->paginate($user_model_name, $filter);

            $this->set('users', $users);
            $this->render('check_user_permissions');
        } else {
            $role_display_field = $this->AclManager->setDisplayName($role_model_name, Configure:: read('acl.aro.role.display_field'));

            $this->set('role_display_field', $role_display_field);

            $this->{$role_model_name}->recursive = -1;
            $roles = $this->{$role_model_name}->find('all', array('order' => $role_display_field, 'contain' => false, 'recursive' => -1));

            $this->{$user_model_name}->recursive = -1;
            $user = $this->{$user_model_name}->get($user_id);

            $permissions = array();

            /*
             * Check if the user exists in the ARO table
             */
            $user_aro = $this->Acl->Aro->node($user);
            if (!$user_aro) {
                $display_user = $this->{$user_model_name}->find('first', array('conditions' => array($user_model_name . '.id' => $user_id, 'contain' => false, 'recursive' => -1)));
                $this->Flash->error(sprintf(__d('acl', "The user '%s' does not exist in the ARO table"), $display_user->$user_display_field));
            } else {
                $actions = $this->AclReflector->getAllActions();

                foreach ($actions as $full_action) {
                    if (!isset($this->params['named']['ajax'])) {
                        $aco_node = $this->Acl->Aco->node($this->AclReflector->getRootNodeName() . '/' . $full_action);
                        if ($aco_node) {
                            $authorized = $this->Acl->check($user, $this->AclReflector->getRootNodeName() . '/' . $full_action);

                            $permissions[$full_action][$user->{$this->AclManager->getUserPrimaryKeyName()}] = $authorized ? 1 : 0;
                        }
                    }
                }

                /*
                 * Check if the user has specific permissions
                 */
                $count = $this->Acl->adapter()->Permission->find()
                    ->where([
                        'aro_id' => $user_aro->toArray()[0]->id
                    ])
                    ->all();
                if ($count->count()) {
                    $this->set('user_has_specific_permissions', true);
                } else {
                    $this->set('user_has_specific_permissions', false);
                }
            }

            $this->set('user', $user);
            $this->set('roles', $roles);
            $this->set('actions', $actions);
            $this->set('permissions', $permissions);

            if ($ajax) {
                $this->render('ajax_user_permissions');
            }
        }
    }

    public function emptyPermissions()
    {
        if ($this->Acl->adapter()->Permission->deleteAll(array('Permission.id > ' => 0))) {
            $this->Flash->success(__d('acl', 'The permissions have been cleared'));
        } else {
            $this->Flash->error(__d('acl', 'The permissions could not be cleared'));
        }

        $this->_returnToReferer();
    }

    public function clearUserSpecificPermissions($user_id)
    {
        $ref = [
            'model' => Configure:: read('acl.aro.user.model'),
            'foreign_key' => $user_id
        ];
        $node = $this->Acl->Aro->node($ref);

        /*
         * Check if the user exists in the ARO table
         */
        if (!$node) {
            $this->Flash->error(sprintf(__d('acl', "The user '%s' does not exist in the ARO table"), $user_id));
        } else {
            if ($this->Acl->adapter()->Permission->deleteAll(array('aro_id' => $node->toArray()[0]->id))) {
                $this->Flash->success(__d('acl', 'The specific permissions have been cleared'));
            } else {
                $this->Flash->error(__d('acl', 'The specific permissions could not be cleared'));
            }
        }

        $this->_returnToReferer();
    }

    public function grantAllControllers($role_id)
    {
        $ref = [
            'model' => Configure:: read('acl.aro.role.model'),
            'foreign_key' => $role_id
        ];
        $node = $this->Acl->Aro->node($ref);

        /*
         * Check if the Role exists in the ARO table
         */
        if (!$node) {
            $this->Flash->error(sprintf(__d('acl', "The role '%s' does not exist in the ARO table"), $role_id)); // TODO FIX options
        } else {
            //Allow to everything
            $this->Acl->allow($ref, $this->AclReflector->getRootNodeName()); // TODO FIX ME
        }

        $this->_returnToReferer();
    }

    public function denyAllControllers($role_id)
    {
        $ref = [
            'model' => Configure:: read('acl.aro.role.model'),
            'foreign_key' => $role_id
        ];
        $node = $this->Acl->Aro->node($ref);

        /*
         * Check if the Role exists in the ARO table
         */
        if (!$node) {
            $this->Flash->error(sprintf(__d('acl', "The role '%s' does not exist in the ARO table"), $role_id)); // TODO FIX options
        } else {
            //Deny everything
            $this->Acl->deny($ref, $this->AclReflector->getRootNodeName());
        }

        $this->_returnToReferer();
    }

    public function getRoleControllerPermission($role_id)
    {
        $role = $this->{Configure:: read('acl.aro.role.model')};

        $role_data = $role->get($role_id);

        $aro_node = $this->Acl->Aro->node($role_data);
        if ($aro_node) {
            $plugin_name = isset($this->params['named']['plugin']) ? $this->params['named']['plugin'] : '';
            $controller_name = $this->params['named']['controller'];
            $controller_actions = $this->AclReflector->get_controller_actions($controller_name);

            $role_controller_permissions = array();

            foreach ($controller_actions as $action_name) {
                $aco_path = $plugin_name;
                $aco_path .= !$aco_path ? $controller_name : '/' . $controller_name;
                $aco_path .= '/' . $action_name;

                $aco_node = $this->Acl->Aco->node($this->AclReflector->getRootNodeName() . '/' . $aco_path);
                if ($aco_node) {
                    $authorized = $this->Acl->check($role_data, $this->AclReflector->getRootNodeName() . '/' . $aco_path);
                    $role_controller_permissions[$action_name] = $authorized;
                } else {
                    $role_controller_permissions[$action_name] = -1;
                }
            }
        } else {
            //$this->set('acl_error', true);
            //$this->set('acl_error_aro', true);
        }

        if ($this->getRequest()->is('Ajax')) {
            Configure::write('debug', 0); //-> to disable printing of generation time preventing correct JSON parsing
            echo json_encode($role_controller_permissions);
            $this->autoRender = false;
        } else {
            $this->_returnToReferer();
        }
    }

    public function grantRolePermission($role_id, ...$aliases)
    {
        $aco_path = implode('/', $aliases);

        /*
         * Check if the role exists in the ARO table
         */
        $aro_node = $this->{Configure:: read('acl.aro.role.model')}->get($role_id);
        if ($aro_node) {
            if (!$this->Acl->allow($aro_node, $aco_path)) {
                $this->set('acl_error', true);
            }
        } else {
            $this->set('acl_error', true);
            $this->set('acl_error_aro', true);
        }

        $this->set('role_id', $role_id);
        $this->set('action', $aco_path);

        if ($this->getRequest()->is('Ajax')) {
            $this->render('ajax_role_granted');
        } else {
            $this->_returnToReferer();
        }
    }

    public function denyRolePermission($role_id, ...$aliases)
    {
        $aco_path = implode('/', $aliases);

        /*
         * Check if the role exists in the ARO table
         */
        $aro_node = $this->{Configure:: read('acl.aro.role.model')}->get($role_id);
        if ($aro_node) {
            if (!$this->Acl->deny($aro_node, $aco_path)) {
                $this->set('acl_error', true);
            }
        } else {
            $this->set('acl_error', true);
        }

        $this->set('role_id', $role_id);
        $this->set('action', $aco_path);

        if ($this->getRequest()->is('Ajax')) {
            $this->render('ajax_role_denied');
        } else {
            $this->_returnToReferer();
        }
    }

    public function getUserControllerPermission($user_id)
    {
        $user = $this->{Configure:: read('acl.aro.user.model')};

        $user_data = $user->get($user_id);

        $aro_node = $this->Acl->Aro->node($user_data);
        if ($aro_node) {
            $plugin_name = isset($this->params['named']['plugin']) ? $this->params['named']['plugin'] : '';
            $controller_name = $this->params['named']['controller'];
            $controller_actions = $this->AclReflector->get_controller_actions($controller_name);

            $user_controller_permissions = array();

            foreach ($controller_actions as $action_name) {
                $aco_path = $plugin_name;
                $aco_path .= !$aco_path ? $controller_name : '/' . $controller_name;
                $aco_path .= '/' . $action_name;

                $aco_node = $this->Acl->Aco->node($this->AclReflector->getRootNodeName() . '/' . $aco_path);
                if ($aco_node) {
                    $authorized = $this->Acl->check($user_data, $this->AclReflector->getRootNodeName() . '/' . $aco_path);
                    $user_controller_permissions[$action_name] = $authorized;
                } else {
                    $user_controller_permissions[$action_name] = -1;
                }
            }
        } else {
            //$this->set('acl_error', true);
            //$this->set('acl_error_aro', true);
        }

        if ($this->getRequest()->is('Ajax')) {
            Configure::write('debug', 0); //-> to disable printing of generation time preventing correct JSON parsing
            echo json_encode($user_controller_permissions);
            $this->autoRender = false;
        } else {
            $this->_returnToReferer();
        }
    }

    public function grantUserPermission($user_id, ...$aliases)
    {
        $aco_path = implode('/', $aliases);

        /*
         * Check if the user exists in the ARO table
         */
        $aro_node = $this->{Configure:: read('acl.aro.user.model')}->get($user_id);
        if ($aro_node) {
            if (!$this->Acl->allow($aro_node, $aco_path)) {
                $this->set('acl_error', true);
            }
        } else {
            $this->set('acl_error', true);
        }

        $this->set('user_id', $user_id);
        $this->set('action', $aco_path);

        if ($this->getRequest()->is('Ajax')) {
            $this->render('ajax_user_granted');
        } else {
            $this->_returnToReferer();
        }
    }

    public function denyUserPermission($user_id, ...$aliases)
    {
        $aco_path = implode('/', $aliases);

        /*
         * Check if the user exists in the ARO table
         */
        $aro_node = $this->{Configure:: read('acl.aro.user.model')}->get($user_id);
        if ($aro_node) {
            if (!$this->Acl->deny($aro_node, $aco_path)) {
                $this->set('acl_error', true);
            }
        } else {
            $this->set('acl_error', true);
        }

        $this->set('user_id', $user_id);
        $this->set('action', $aco_path);

        if ($this->getRequest()->is('Ajax')) {
            $this->render('ajax_user_denied');
        } else {
            $this->_returnToReferer();
        }
    }
}
