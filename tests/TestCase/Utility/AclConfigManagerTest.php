<?php

namespace AclManager\Test\TestCase\Utility;

use AclManager\Utility\AclConfigManager;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * AclManager\AclExtras Test Case
 */
class AclConfigManagerTest extends TestCase
{

    /**
     * Test getRolePrimaryKey method
     *
     * @return void
     */
    public function testGetRolePrimaryKey()
    {
        Configure::write('acl.aro.role.primary_key', 'role_primary_key');
        $this->assertEquals('role_primary_key', AclConfigManager::getRolePrimaryKey());
    }

    /**
     * Test getUserPrimaryKey method
     *
     * @return void
     */
    public function testGetUserPrimaryKey()
    {
        Configure::write('acl.aro.user.primary_key', 'user_primary_key');
        $this->assertEquals('user_primary_key', AclConfigManager::getUserPrimaryKey());
    }

    /**
     * Test getRoleForeignKey method
     *
     * @return void
     */
    public function testGetRoleForeignKey()
    {
        Configure::write('acl.aro.role.foreign_key', 'role_foreign_key');
        $this->assertEquals('role_foreign_key', AclConfigManager::getRoleForeignKey());
    }

    /**
     * Test getRoleModel
     *
     * @return void
     */
    public function testGetRoleModel()
    {
        Configure::write('acl.aro.role.model', 'Roles');
        $this->assertEquals('Roles', AclConfigManager::getRoleModel());

        Configure::write('acl.aro.role.model', 'Users.Roles');
        $this->assertEquals('Users.Roles', AclConfigManager::getRoleModel());
    }

    /**
     * Test getUserModel
     *
     * @return void
     */
    public function testGetUserModel()
    {
        Configure::write('acl.aro.user.model', 'Users');
        $this->assertEquals('Users', AclConfigManager::getUserModel());

        Configure::write('acl.aro.user.model', 'Users.Users');
        $this->assertEquals('Users.Users', AclConfigManager::getUserModel());
    }

    /**
     * Test getRoleModelAlias
     *
     * @return void
     */
    public function testGetRoleModelAlias()
    {
        Configure::write('acl.aro.role.model', 'Roles');
        $this->assertEquals('Roles', AclConfigManager::getRoleModelAlias());

        Configure::write('acl.aro.role.model', 'Users.Roles');
        $this->assertEquals('Roles', AclConfigManager::getRoleModelAlias());
    }

    /**
     * Test getUserModelAlias
     *
     * @return void
     */
    public function testGetUserModelAlias()
    {
        Configure::write('acl.aro.user.model', 'Users');
        $this->assertEquals('Users', AclConfigManager::getUserModelAlias());

        Configure::write('acl.aro.user.model', 'Users.Users');
        $this->assertEquals('Users', AclConfigManager::getUserModelAlias());
    }
}
