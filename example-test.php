<?php

require './single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;

class MyTest extends TestCase {
    public function testSomething() {
        $this->assertSame(2, 1 + 1);
    }
}

(new MyTest())->runTests();
