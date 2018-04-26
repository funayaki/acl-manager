<?php
/**
 *
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 *
 * @property AclManagerComponent $AclManager
 */
namespace AclManager\Controller;

use AclManager\Controller\Component\AclManagerComponent;
use App\Controller\AppController as BaseController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Utility\Inflector;

/**
 * @property AclManagerComponent AclManager
 */
class AppController extends BaseController
{
    var $components = array('RequestHandler', 'Auth', 'Acl.Acl', 'AclManager.AclManager', 'AclManager.AclReflector');

    public function beforeFilter(Event $event)
    {
        parent:: beforeFilter($event);

        $this->_checkConfig();
        $this->_checkFilesUpdates();
    }

    private function _checkConfig()
    {
        $role_model_name = Configure:: read('acl.aro.role.model');

        if ($role_model_name) {
            $this->set('role_model_name', $role_model_name);
            $this->set('user_model_name', Configure:: read('acl.aro.user.model'));
            $this->set('role_pk_name', $this->AclManager->getRolePrimaryKeyName());
            $this->set('user_pk_name', $this->AclManager->getUserPrimaryKeyName());
            $this->set('role_fk_name', $this->AclManager->getRoleForeignKeyName());

            $this->_authorizeAdmins();

            if (Configure:: read('acl.check_act_as_requester')) {
                $is_requester = true;

                if (!$this->AclManager->checkUserModelActsAsAclRequester(Configure:: read('acl.aro.user.model'))) {
                    $this->set('model_is_not_requester', false);
                    $is_requester = false;
                }

                if (!$this->AclManager->checkUserModelActsAsAclRequester(Configure:: read('acl.aro.role.model'))) {
                    $this->set('role_is_not_requester', false);
                    $is_requester = false;
                }

                if (!$is_requester) {
                    $this->render('/Aros/admin_not_acl_requester');
                }
            }
        } else {
            $this->Session->setFlash(__d('acl', 'The role model name is unknown. The ACL plugin bootstrap.php file has to be loaded in order to work. (see the README file)'), 'flash_error', null, 'plugin_acl');
        }
    }

    protected function _checkFilesUpdates()
    {
        $prefix = $this->request->getParam('prefix');
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');

        if ($controller != 'Acos'
            || !(($prefix == 'admin' && $action == 'synchronize') ||
                ($prefix == 'admin' && $action == 'pruneAcos') ||
                ($prefix == 'admin' && $action == 'buildAcl'))
        ) {
            if ($this->AclManager->controllerHashFileIsOutOfSync()) {
                $missing_aco_nodes = $this->AclManager->getMissingACOs();
                $nodes_to_prune = $this->AclManager->getACOsToPrune();

                $has_updates = false;

                if (count($missing_aco_nodes) > 0) {
                    $has_updates = true;
                }

                if (count($nodes_to_prune) > 0) {
                    $has_updates = true;
                }

                $this->set('nodes_to_prune', $nodes_to_prune);
                $this->set('missing_aco_nodes', $missing_aco_nodes);

                if ($has_updates) {
                    $this->render('/Admin/Acos/has_updates');
                    $this->response->send();
                    $this->AclManager->updateControllersHashFile();
                    die();
                } else {
                    $this->AclManager->updateControllersHashFile();
                }
            }
        }
    }

    private function _authorizeAdmins()
    {
        $authorized_role_ids = Configure:: read('acl.role.access_plugin_role_ids');
        $authorized_user_ids = Configure:: read('acl.role.access_plugin_user_ids');

        $model_role_fk = $this->AclManager->getRoleForeignKeyName();

        if (in_array($this->Auth->user($model_role_fk), $authorized_role_ids)
            || in_array($this->Auth->user($this->AclManager->getUserPrimaryKeyName()), $authorized_user_ids)
        ) {
            // Allow all actions. CakePHP 2.0
            $this->Auth->allow('*');

            // Allow all actions. CakePHP 2.1
            $this->Auth->allow();
        }
    }

    protected function _getPassedACOPath()
    {
        $aco_path = isset($this->params['named']['plugin']) ? $this->params['named']['plugin'] : '';
        $aco_path .= !$aco_path ? $this->params['named']['controller'] : '/' . $this->params['named']['controller'];
        $aco_path .= '/' . $this->params['named']['action'];

        return $aco_path;
    }

    protected function _setACOVariables()
    {
        $this->set('plugin', isset($this->params['named']['plugin']) ? $this->params['named']['plugin'] : '');
        $this->set('controller_name', $this->params['named']['controller']);
        $this->set('action', $this->params['named']['action']);
    }

    protected function _returnToReferer()
    {
        $this->redirect($this->referer(array('action' => 'index')));
    }
}
