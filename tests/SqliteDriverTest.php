<?php

require_once 'vendor/autoload.php';

use PicoDb\Driver\Sqlite;

class SqliteDriverTest extends PHPUnit_Framework_TestCase
{
    private $driver;

    public function setUp()
    {
        $this->driver = new Sqlite(array('filename' => ':memory:'));
    }

    /**
     * @expectedException LogicException
     */
    public function testMissingRequiredParameter()
    {
        new Sqlite(array());
    }

    public function testDuplicateKeyError()
    {
        $this->assertFalse($this->driver->isDuplicateKeyError(1234));
        $this->assertTrue($this->driver->isDuplicateKeyError(23000));
    }

    public function testOperator()
    {
        $this->assertEquals('LIKE', $this->driver->getOperator('LIKE'));
        $this->assertEquals('LIKE', $this->driver->getOperator('ILIKE'));
        $this->assertEquals('', $this->driver->getOperator('FOO'));
    }

    public function testSchemaVersion()
    {
        $this->assertEquals(0, $this->driver->getSchemaVersion());

        $this->driver->setSchemaVersion(1);
        $this->assertEquals(1, $this->driver->getSchemaVersion());

        $this->driver->setSchemaVersion(42);
        $this->assertEquals(42, $this->driver->getSchemaVersion());
    }

    public function testLastInsertId()
    {
        $this->assertEquals(0, $this->driver->getLastId());

        $this->driver->getConnection()->exec('CREATE TABLE foobar (id INTEGER PRIMARY KEY, something TEXT)');
        $this->driver->getConnection()->exec('INSERT INTO foobar (something) VALUES (1)');

        $this->assertEquals(1, $this->driver->getLastId());
    }

    public function testEscape()
    {
        $this->assertEquals('"foobar"', $this->driver->escape('foobar'));
    }
}
