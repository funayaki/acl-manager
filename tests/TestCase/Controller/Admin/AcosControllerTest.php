<?php
namespace AclManager\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * AclManager\Controller\Admin\AcosController Test Case
 */
class AcosControllerTest extends IntegrationTestCase
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

        $this->get('/admin/acl_manager/acos');
        $this->assertResponseOk();
    }

    /**
     * Test emptyAcosWithDryRun method
     *
     * @return void
     */
    public function testEmptyAcosWithDryRun()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/acos/empty_acos');
        $this->assertResponseOk();

        $Acos = TableRegistry::get('Acos');
        $this->assertNotEquals(0, $Acos->find()->count());
    }

    /**
     * Test emptyAcos method
     *
     * @return void
     */
    public function testEmptyAcos()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/acos/empty_acos/run');
        $this->assertResponseOk();

        $Acos = TableRegistry::get('Acos');
        $this->assertEquals(0, $Acos->find()->count());
    }

    /**
     * Test buildAcl method
     *
     * @return void
     */
    public function testBuildAcl()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/acos/build_acl');
        $this->assertResponseOk();

        $this->get('/admin/acl_manager/acos/build_acl/run');
        $this->assertResponseOk();
    }

    /**
     * Test pruneAcos method
     *
     * @return void
     */
    public function testPruneAcos()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/acos/prune_acos');
        $this->assertResponseOk();

        $this->get('/admin/acl_manager/acos/prune_acos/run');
        $this->assertResponseOk();
    }

    /**
     * Test synchronize method
     *
     * @return void
     */
    public function testSynchronize()
    {
        $this->setUpAuth();

        $this->get('/admin/acl_manager/acos/synchronize');
        $this->assertResponseOk();

        $this->get('/admin/acl_manager/acos/synchronize/run');
        $this->assertResponseOk();
    }
}

