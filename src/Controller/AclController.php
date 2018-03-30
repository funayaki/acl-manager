<?php
/**
 *
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 */
namespace Controller;

use AclManager\Controller\AppController;

class AclController extends AppController
{

    var $name = 'Acl';
    var $uses = null;

    function index()
    {
        $this->redirect('/admin/acl/aros');
    }

    function admin_index()
    {
        $this->redirect('/admin/acl/acos');
    }

}

?>