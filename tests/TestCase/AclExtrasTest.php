<?php
namespace AclManager\Test\TestCase;

use AclManager\AclExtras;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * AclManager\AclExtras Test Case
 */
class AclExtrasTest extends TestCase
{

    /**
     * Test subject
     *
     * @var AclExtras
     */
    public $AclExtras;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->AclExtras = new AclExtras();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AclExtras);

        parent::tearDown();
    }

    public function testGetActionsList()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testGetRootNodeName()
    {
        $this->assertEquals($this->AclExtras->getRootNodeName(), 'controllers');
    }
}

