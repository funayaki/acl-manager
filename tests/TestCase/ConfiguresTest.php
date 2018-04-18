<?php
namespace AclManager\Test\TestCase;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Composer\Config;

/**
 * AclManager\Configures Test Case
 */
class ConfiguresTest extends TestCase
{

    public function testConfiguresNotOverriding()
    {
        $expect = [
            'aro' => [
                'role' => [
                    'model' => '',
                    'primary_key' => '',
                    'foreign_key' => '',
                    'display_field' => ''
                ],
                'user' => [
                    'model' => '',
                    'primary_key' => '',
                ],
            ],
            'role' => [
                'access_plugin_role_ids' => [],
                'access_plugin_user_ids' => [],
            ],
            'user' => [
                'display_name' => ''
            ],
            'check_act_as_requester' => '',
        ];

        Configure::write('acl', $expect);
        include dirname(dirname(dirname(__FILE__))) . '/config/bootstrap.php';
        $this->assertEquals($expect, Configure::read('acl'));
    }
}

