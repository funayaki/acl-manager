<?php
namespace AclManager\Test\TestCase\Controller\Component;

use AclManager\Controller\Component\AclManagerComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * AclManager\Controller\Component\AclManagerComponent Test Case
 */
class AclManagerComponentTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acl_manager.acos',
    ];

    /**
     * Test subject
     *
     * @var \AclManager\Controller\Component\AclManagerComponent
     */
    public $AclManager;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->AclManager = new AclManagerComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AclManager);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test check_user_model_acts_as_acl_requester method
     *
     * @return void
     */
    public function testCheckUserModelActsAsAclRequester()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test set_display_name method
     *
     * @return void
     */
    public function testSetDisplayName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test get_stored_controllers_hashes method
     *
     * @return void
     */
    public function testGetStoredControllersHashes()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test get_current_controllers_hashes method
     *
     * @return void
     */
    public function testGetCurrentControllersHashes()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test get_missing_acos method
     *
     * @return void
     */
    public function testGetMissingAcos()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test create_acos method
     *
     * @return void
     */
    public function testCreateAcos()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test update_controllers_hash_file method
     *
     * @return void
     */
    public function testUpdateControllersHashFile()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test controller_hash_file_is_out_of_sync method
     *
     * @return void
     */
    public function testControllerHashFileIsOutOfSync()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test get_acos_to_prune method
     *
     * @return void
     */
    public function testGetAcosToPrune()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test prune_acos method
     *
     * @return void
     */
    public function testPruneAcos()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test save_permission method
     *
     * @return void
     */
    public function testSavePermission()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test set_session_permissions method
     *
     * @return void
     */
    public function testSetSessionPermissions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getExistingActionsInDatabase method
     *
     * @return void
     */
    public function testGetExistingActionsInDatabase()
    {
        $actual = $this->AclManager->getActionsInDatabase();
        $expected = [
            'Roles/index',
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test getRolePrimaryKeyName method
     *
     * @return void
     */
    public function testGetRolePrimaryKeyName()
    {
        $this->assertEquals('id', $this->AclManager->getRolePrimaryKeyName());
    }

    /**
     * Test getUserPrimaryKeyName method
     *
     * @return void
     */
    public function testGetUserPrimaryKeyName()
    {
        $this->assertEquals('id', $this->AclManager->getUserPrimaryKeyName());
    }

    /**
     * Test getRoleForeignKeyName method
     *
     * @return void
     */
    public function testGetRoleForeignKeyName()
    {
        $this->assertEquals('role_id', $this->AclManager->getRoleForeignKeyName());
    }
}
