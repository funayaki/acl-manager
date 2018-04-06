<?php
/**
 *
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 *
 * @property AclManagerComponent $AclManager
 */
namespace AclManager\Controller\Admin;

use AclManager\Controller\AppController;

class AcosController extends AppController
{

    /**
     *
     */
    public function index()
    {

    }

    /**
     * @param null $run
     */
    public function emptyAcos($run = null)
    {
        /*
         * Delete ACO with 'alias' controllers
         * -> all ACOs belonging to the actions tree will be deleted, but eventual ACO that are not actions will be kept
         */
        $controller_aco = $this->Acl->Aco->findByAlias('controllers')->first();

        if (!empty($controller_aco)) {
            $this->set('actions_exist', true);

            if (isset($run)) {
                if ($this->Acl->Aco->delete($controller_aco)) {
                    $this->set('actions_exist', false);

                    $this->Flash->success(__d('acl', 'The actions in the ACO table have been deleted'));
                } else {
                    $this->Flash->error(__d('acl', 'The actions in the ACO table could not be deleted'));
                }

                $this->set('run', true);
            } else {
                $this->set('run', false);
            }
        } else {
            $this->set('actions_exist', false);
        }
    }

    /**
     * @param null $run
     */
    public function buildAcl($run = null)
    {
        if (isset($run)) {
            $logs = $this->AclManager->createACOs();

            $this->set('logs', $logs);
            $this->set('run', true);
        } else {
            $missing_aco_nodes = $this->AclManager->getMissingACOs();

            $this->set('missing_aco_nodes', $missing_aco_nodes);

            $this->set('run', false);
        }
    }

    /**
     * @param null $run
     */
    public function pruneAcos($run = null)
    {
        if (isset($run)) {
            $logs = $this->AclManager->pruneACOs();

            $this->set('logs', $logs);
            $this->set('run', true);
        } else {
            $nodes_to_prune = $this->AclManager->getACOsToPrune();

            $this->set('nodes_to_prune', $nodes_to_prune);

            $this->set('run', false);
        }
    }

    /**
     * @param null $run
     */
    public function synchronize($run = null)
    {
        if (isset($run)) {
            $prune_logs = $this->AclManager->pruneACOs();
            $create_logs = $this->AclManager->createACOs();

            $this->set('create_logs', $create_logs);
            $this->set('prune_logs', $prune_logs);

            $this->set('run', true);
        } else {
            $nodes_to_prune = $this->AclManager->getACOsToPrune();
            $missing_aco_nodes = $this->AclManager->getMissingACOs();

            $this->set('nodes_to_prune', $nodes_to_prune);
            $this->set('missing_aco_nodes', $missing_aco_nodes);

            $this->set('run', false);
        }
    }
}
