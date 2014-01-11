<?php
namespace EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest;

use EddieJaoude\Zf2Logger\Module;

/**
 * Class ModuleTest
 *
 * @package EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

    private $instance;

    public function setUp()
    {
        $this->instance = new Module();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\EddieJaoude\Zf2Logger\Module', $this->instance);
    }
}
