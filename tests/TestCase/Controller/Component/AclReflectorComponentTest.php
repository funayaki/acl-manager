<?php
namespace AclManager\Test\TestCase\Controller\Component;

use AclManager\Controller\Component\AclReflectorComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * AclManager\Controller\Component\AclReflectorComponent Test Case
 */
class AclReflectorComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \AclManager\Controller\Component\AclReflectorComponent
     */
    public $AclReflectorComponent;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->AclReflectorComponent = new AclReflectorComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AclReflectorComponent);

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
     * Test getControllerList method
     *
     * @return void
     */
    public function testGetControllerList()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test get_all_controllers method
     *
     * @return void
     */
    public function testGetAllControllers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test get_all_actions method
     *
     * @return void
     */
    public function testGetAllActions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getRootNodeName method
     *
     * @return void
     */
    public function testGetRootNodeName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getActionList method
     *
     * @return void
     */
    public function testGetActionList()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getControllerName method
     *
     * @return void
     */
    public function testGetControllerName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getControllerClassName method
     *
     * @return void
     */
    public function testGetControllerClassName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

