<?php

namespace AclManager\Utility;

use Cake\Core\Configure;
use Cake\Utility\Inflector;

class AclConfigManager
{

    /**
     * @return mixed
     */
    public static function getRolePrimaryKey()
    {
        return Configure::read('acl.aro.role.primary_key');
    }

    /**
     * @return mixed|string
     */
    public static function getUserPrimaryKey()
    {
        return Configure::read('acl.aro.user.primary_key');
    }

    public static function getRoleForeignKey()
    {
        return Configure::read('acl.aro.role.foreign_key');
    }

    /**
     * @return mixed
     */
    public static function getRoleModel()
    {
        return Configure::read('acl.aro.role.model');
    }

    /**
     * @return mixed
     */
    public static function getUserModel()
    {
        return Configure::read('acl.aro.user.model');
    }

    /**
     * @return mixed
     */
    public static function getRoleModelAlias()
    {
        list(, $name) = pluginSplit(Configure::read('acl.aro.role.model'));
        return $name;
    }

    /**
     * @return mixed
     */
    public static function getUserModelAlias()
    {
        list(, $name) = pluginSplit(Configure::read('acl.aro.user.model'));
        return $name;
    }
}