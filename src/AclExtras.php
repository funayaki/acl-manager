<?php
namespace AclManager;

use Acl\AclExtras as BaseAclExtras;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;

class AclExtras extends BaseAclExtras
{

    /**
     * @param $className
     * @param null $pluginPath
     * @param null $prefixPath
     * @return array|bool
     */
    public function getActionList($className, $pluginPath = null, $prefixPath = null)
    {
        $excludes = $this->_getCallbacks($className, $pluginPath, $prefixPath);
        $baseMethods = get_class_methods(new Controller);
        $namespace = $this->_getNamespace($className, $pluginPath, $prefixPath);
        $methods = get_class_methods(new $namespace);
        if ($methods == null) {
            $this->err(__d('acl', 'Unable to get methods for {0}', $className));

            return false;
        }
        $actions = array_diff($methods, $baseMethods);
        $actions = array_diff($actions, $excludes);
        foreach ($actions as $key => $action) {
            if (strpos($action, '_', 0) === 0) {
                continue;
            }
            $actions[$key] = $action;
        }

        return $actions;
    }

    /**
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->rootNode;
    }
}