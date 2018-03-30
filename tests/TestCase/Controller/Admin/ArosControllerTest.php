<?php
namespace AclManager\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * AclManager\Controller\Admin\ArosController Test Case
 */
class ArosControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acl_manager.acos',
        'plugin.acl_manager.aros',
        'plugin.acl_manager.aros_acos',
        'plugin.acl_manager.roles',
        'plugin.acl_manager.users'
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Roles = TableRegistry::get(Configure::read('acl.aro.role.model'));
        $this->Users = TableRegistry::get(Configure::read('acl.aro.user.model'));
    }

    public function tearDown()
    {
        unset($this->Roles);
        unset($this->Users);
    }

    public function setUpAuth()
    {
        $this->session(['Auth.User.id' => 1]);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros');
        $this->assertResponseOk();
    }

    /**
     * Test check method
     *
     * @return void
     */
    public function testCheck()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/check');
        $this->assertResponseOk();

        $this->get('/admin/acl_manager/aros/check/run');
        $this->assertResponseOk();
    }

    /**
     * Test users method
     *
     * @return void
     */
    public function testUsers()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/users');
        $this->assertResponseOk();
    }

    /**
     * Test updateUserRole method
     *
     * @return void
     */
    public function testUpdateUserRole()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/update_user_role/1/2');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test updateUserRoleWithInvalidRole method
     *
     * @return void
     */
    public function testUpdateUserRoleWithInvalidRole()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/update_user_role/1/0');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test ajaxRolePermissions method
     *
     * @return void
     */
    public function testAjaxRolePermissions()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/ajax_role_permissions');
        $this->assertResponseOk();
    }

    /**
     * Test rolePermissions method
     *
     * @return void
     */
    public function testRolePermissions()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/role_permissions');
        $this->assertResponseOk();
    }

    /**
     * Test userPermissions method
     *
     * @return void
     */
    public function testUserPermissions()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/user_permissions');
        $this->assertResponseOk();
    }

    /**
     * Test emptyPermissions method
     *
     * @return void
     */
    public function testEmptyPermissions()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/empty_permissions');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test clearUserSpecificPermissions method
     *
     * @return void
     */
    public function testClearUserSpecificPermissions()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/clear_user_specific_permissions/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test grantAllControllers method
     *
     * @return void
     */
    public function testGrantAllControllers()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/grant_all_controllers/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test denyAllControllers method
     *
     * @return void
     */
    public function testDenyAllControllers()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/deny_all_controllers/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test getRoleControllerPermission method
     *
     * @return void
     */
    public function testGetRoleControllerPermission()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/get_role_controller_permission/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test grantRolePermission method
     *
     * @return void
     */
    public function testGrantRolePermission()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/grant_all_controllers/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test denyRolePermission method
     *
     * @return void
     */
    public function testDenyRolePermission()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/deny_all_controllers/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test getUserControllerPermission method
     *
     * @return void
     */
    public function testGetUserControllerPermission()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/get_user_controller_permission/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test grantUserPermission method
     *
     * @return void
     */
    public function testGrantUserPermission()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/grant_user_permission/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }

    /**
     * Test denyUserPermission method
     *
     * @return void
     */
    public function testDenyUserPermission()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/aros/deny_user_permission/1');
        $this->assertRedirect('/admin/acl_manager/aros');
    }
}

