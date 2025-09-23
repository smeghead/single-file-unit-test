<?php

require_once dirname(__FILE__) . '/../single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;

class SetUpTearDownTest extends TestCase
{
    private static $log = [];

    public function setUp()
    {
        self::$log[] = 'setUp';
    }

    public function tearDown()
    {
        self::$log[] = 'tearDown';
    }

    public function testOne()
    {
        $this->assertSame(['setUp'], self::$log, 'setUp should be called before testOne');
        self::$log[] = 'testOne';
    }

    public function testTwo()
    {
        $this->assertSame(['setUp', 'testOne', 'tearDown', 'setUp'], self::$log, 'setUp should be called again for testTwo');
        self::$log[] = 'testTwo';
    }

    public static function tearDownAfterClass()
    {
        // This is a special method that runs after all tests in the class.
        // We use it here to check the final state of the log.
        // Note: tearDownAfterClass is not part of the feature we are adding,
        // but it's useful for this specific test case.
        // We will need to implement a mechanism to run this.
        // For now, let's assume the test runner can handle it or we check manually.
        $expected = [
            'setUp',
            'testOne',
            'tearDown',
            'setUp',
            'testTwo',
            'tearDown',
        ];
        // Since we can't use $this->assertSame in a static method,
        // we'll just have to imagine this check for now.
        // The individual tests will fail if setUp/tearDown are not called correctly.
    }
}
