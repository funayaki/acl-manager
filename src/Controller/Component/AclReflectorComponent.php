<?php
namespace AclManager\Controller\Component;

use AclManager\AclExtras;
use Cake\Controller\Component;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * @property AclExtras AclExtras
 */
class AclReflectorComponent extends Component
{
    protected $controllers = [];

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->AclExtras = new AclExtras();
        $this->AclExtras->startup($this->_registry->getController());

        $this->_buildAppControllerList();
        $this->_buildAppPrefixControllerList();
        $this->_buildPluginControllerList();
        $this->_buildPluginPrefixControllerList();
    }

    /**
     *
     */
    protected function _buildAppControllerList()
    {
        $controllers = $this->getControllerList();
        foreach ($controllers as $path => $controller) {
            $this->controllers[$path] = $controller;
        }
    }

    /**
     *
     */
    protected function _buildAppPrefixControllerList()
    {
        foreach (array_keys($this->AclExtras->getPrefixes()) as $prefix) {
            $controllers = $this->getControllerList(null, $prefix);
            foreach ($controllers as $path => $controller) {
                $this->controllers[$path] = $controller;
            }
        }
    }

    /**
     *
     */
    protected function _buildPluginControllerList()
    {
        $pluginPrefixes = $this->AclExtras->getPluginPrefixes();
        foreach (array_keys($pluginPrefixes) as $plugin) {
            $controllers = $this->getControllerList($plugin);
            foreach ($controllers as $path => $controller) {
                $this->controllers[$path] = $controller;
            }
        }
    }

    /**
     *
     */
    protected function _buildPluginPrefixControllerList()
    {
        $pluginPrefixes = $this->AclExtras->getPluginPrefixes();
        foreach (array_keys($pluginPrefixes) as $plugin) {
            foreach (array_keys($pluginPrefixes[$plugin]) as $prefix) {
                $controllers = $this->getControllerList($plugin, $prefix);
                foreach ($controllers as $path => $controller) {
                    $this->controllers[$path] = $controller;
                }
            }
        }
    }

    /**
     * @param null $plugin
     * @param null $prefix
     * @return array
     */
    public function getControllerList($plugin = null, $prefix = null)
    {
        if (!$plugin) {
            $path = App::path('Controller' . (empty($prefix) ? '' : DS . Inflector::camelize($prefix)));
            $dir = new Folder($path[0]);
            $controllers = $dir->find('.*Controller\.php');
        } else {
            $path = App::path('Controller' . (empty($prefix) ? '' : DS . Inflector::camelize($prefix)), $plugin);
            $dir = new Folder($path[0]);
            $controllers = $dir->find('.*Controller\.php');
        }

        $res = [];
        foreach ($controllers as $controller) {
            $path = $dir->pwd() . $controller;
            $res[$path] = [
                'name' => $this->getControllerName($controller),
                'className' => $this->getControllerClassName($controller),
                'plugin' => $plugin,
                'prefix' => $prefix,
            ];
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getAllControllers()
    {
        return $this->controllers;
    }

    /**
     *
     */
    public function getAllActions()
    {
        $actions = [];
        foreach ($this->getAllControllers() as $path => $controller) {
            $controller_actions = $this->getActionList($controller['className'], $controller['plugin'], $controller['prefix']);
            if ($controller_actions) {
                foreach ($controller_actions as $controller_action) {
                    $actions[] = implode('/', array_filter(array(
                        $controller['plugin'],
                        $controller['prefix'],
                        $controller['name'],
                        $controller_action
                    )));
                }
            }
        }

        return $actions;
    }

    /**
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->AclExtras->getRootNodeName();
    }

    /**
     * @param $className
     * @param null $pluginPath
     * @param null $prefixPath
     * @return array|bool
     */
    public function getActionList($className, $pluginPath = null, $prefixPath = null)
    {
        return $this->AclExtras->getActionList($className, $pluginPath, $prefixPath);
    }

    /**
     * @param $controller
     * @return mixed
     */
    public function getControllerName($controller)
    {
        $tmp = explode('/', $controller);
        return str_replace('Controller.php', '', array_pop($tmp));
    }

    /**
     * @param $controllerName
     * @return string
     */
    public function getControllerClassName($controllerName)
    {
        return $this->getControllerName($controllerName) . 'Controller';
    }
}
