<?php
namespace AclManager\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AcosFixture
 *
 */
class AcosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'parent_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'model' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'foreign_key' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'alias' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'lft' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'rght' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'lft' => ['type' => 'index', 'columns' => ['lft', 'rght'], 'length' => []],
            'alias' => ['type' => 'index', 'columns' => ['alias'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'parent_id' => null,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'controllers',
            'lft' => 1,
            'rght' => 6
        ],
        [
            'id' => 2,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Roles',
            'lft' => 2,
            'rght' => 5
        ],
        [
            'id' => 3,
            'parent_id' => 2,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'index',
            'lft' => 3,
            'rght' => 4
        ],
    ];
}

