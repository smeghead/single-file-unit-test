<?php

require_once __DIR__ . '/../single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;
use Smeghead\SingleFileUnitTest\ExpectationFailedException;

/**
 * このテストクラス内で定義された、例外をスローするためのヘルパーメソッド群。
 * runTestから呼び出されるため、メソッド名は 'test' で始めません。
 */
class TargetForRunTest extends TestCase {
    public function methodThrowsExactMessage() {
        throw new \Exception("This is the exact message.");
    }

    public function methodThrowsPartialMessage() {
        throw new \Exception("This message contains the partial text.");
    }

    public function methodThrowsDifferentMessage() {
        throw new \Exception("This is a completely different message.");
    }

    public function methodDoesNotThrow() {
        // 何もスローしない
    }
}


/**
 * expectExceptionMessage の動作をテストするためのテストクラス。
 * runTestメソッドを直接呼び出してテストします。
 */
class ExpectExceptionMessageTest extends TestCase {

    public function testSuccessWhenExactMessageIsThrown() {
        $test = new TargetForRunTest();
        $test->expectExceptionMessage("This is the exact message.");
        
        $result = $test->runTest(TargetForRunTest::class, 'methodThrowsExactMessage');
        
        $this->assertSame('✔ TargetForRunTest::methodThrowsExactMessage (expected exception caught)', $result);
    }

    public function testSuccessWhenPartialMessageIsThrown() {
        $test = new TargetForRunTest();
        $test->expectExceptionMessage("partial text");

        $result = $test->runTest(TargetForRunTest::class, 'methodThrowsPartialMessage');

        $this->assertSame('✔ TargetForRunTest::methodThrowsPartialMessage (expected exception caught)', $result);
    }

    public function testFailureWhenExceptionIsNotThrown() {
        $test = new TargetForRunTest();
        $test->expectExceptionMessage("some error");
        
        $catched = false;
        try {
            $test->runTest(TargetForRunTest::class, 'methodDoesNotThrow');
        } catch (ExpectationFailedException $e) {
            $catched = true;
            $this->assertSame('Failed asserting that exception message [some error] was thrown.', $e->getMessage());
        }
        $this->assertSame(true, $catched, 'ExpectationFailedException should have been thrown.');
    }

    public function testFailureWhenMessageIsDifferent() {
        $test = new TargetForRunTest();
        $test->expectExceptionMessage("an expected message");

        $catched = false;
        try {
            $test->runTest(TargetForRunTest::class, 'methodThrowsDifferentMessage');
        } catch (\Exception $e) {
            // 元の例外が再スローされることを確認
            if ($e instanceof ExpectationFailedException) {
                 // このテストではExpectationFailedExceptionは期待していない
            } else {
                $catched = true;
                $this->assertSame('This is a completely different message.', $e->getMessage());
            }
        }
        $this->assertSame(true, $catched, 'The original exception should have been re-thrown.');
    }
}
