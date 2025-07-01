<?php

require_once __DIR__ . '/../single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;
use Smeghead\SingleFileUnitTest\ResultAccumulator;

class ResultAccumulatorTest extends TestCase
{
    public function testAddFailedTest()
    {
        $accumulator = new ResultAccumulator();
        
        $accumulator->addFailedTest('TestClass::testMethod1');
        $this->assertSame(['TestClass::testMethod1'], $accumulator->getFailedTests(), 'Failed tests should contain added test');
        
        $accumulator->addFailedTest('TestClass::testMethod2');
        $this->assertSame(['TestClass::testMethod1', 'TestClass::testMethod2'], $accumulator->getFailedTests(), 'Failed tests should contain both added tests');
    }
    
    public function testHasFailuresWithoutFailCount()
    {
        $accumulator = new ResultAccumulator();
        
        // Add failed test but don't increment fail count
        $accumulator->addFailedTest('TestClass::testMethod1');
        $this->assertSame(false, $accumulator->hasFailures(), 'hasFailures should be false without fail count increment');
    }
    
    public function testHasFailuresWithFailCount()
    {
        $accumulator = new ResultAccumulator();
        
        $accumulator->incrementFailCount();
        $this->assertSame(true, $accumulator->hasFailures(), 'hasFailures should be true after fail count increment');
    }
    
    public function testMultipleOperations()
    {
        $accumulator = new ResultAccumulator();
        
        // Simulate a test run
        $accumulator->incrementTestCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementAssertionCount();
        
        $accumulator->incrementTestCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementFailCount();
        $accumulator->addFailedTest('SomeTest::testFailingMethod');
        
        $accumulator->incrementTestCount();
        $accumulator->incrementAssertionCount();
        
        // Verify final state
        $this->assertSame(['SomeTest::testFailingMethod'], $accumulator->getFailedTests(), 'Should have 1 failed test');
        $this->assertSame(true, $accumulator->hasFailures(), 'Should have failures');
        $expected = "FAILURES!\nTests: 3 Assertions: 4 Failures: 1.";
        $this->assertSame($expected, $accumulator->getSummaryMessage(), 'Failure message should be formatted correctly');
    }

    public function testGetSummaryMessageSuccess()
    {
        $accumulator = new ResultAccumulator();
        
        $accumulator->incrementTestCount();
        $accumulator->incrementTestCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementAssertionCount();
        
        $expected = "OK (2 tests, 3 assertions)";
        $this->assertSame($expected, $accumulator->getSummaryMessage(), 'Success message should be formatted correctly');
    }

    public function testGetSummaryMessageFailure()
    {
        $accumulator = new ResultAccumulator();
        
        $accumulator->incrementTestCount();
        $accumulator->incrementTestCount();
        $accumulator->incrementTestCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementAssertionCount();
        $accumulator->incrementFailCount();
        $accumulator->addFailedTest('SomeTest::testFailingMethod');
        
        $expected = "FAILURES!\nTests: 3 Assertions: 4 Failures: 1.";
        $this->assertSame($expected, $accumulator->getSummaryMessage(), 'Failure message should be formatted correctly');
    }

    public function testGetSummaryMessageNoTestsNoFailures()
    {
        $accumulator = new ResultAccumulator();
        
        $expected = "OK (0 tests, 0 assertions)";
        $this->assertSame($expected, $accumulator->getSummaryMessage(), 'Empty success message should be formatted correctly');
    }
}
