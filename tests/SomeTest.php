<?php

require_once __DIR__ . '/../TestCase.php';

use Smeghead\SingleFileUnitTest\TestCase;

class Some {
    public function add($a, $b) { return $a + $b; }
    public function invalidOperation() { throw new Exception("Invalid argument"); }
}

class SomeTest extends TestCase {
    public function test足し算の結果が正しいこと() {
        $sut = new Some();
        $this->assertSame(3, $sut->add(1, 2), '1 + 2 should be 3');
    }

    public function test例外が発生すること() {
        $this->expectExceptionMessage('Invalid argument');
        $sut = new Some();
        $sut->invalidOperation();
    }
}
