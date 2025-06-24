<?php

require_once __DIR__ . '/../single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;
use Smeghead\SingleFileUnitTest\ResultAccumulator;

class ResultAccumulatorTest extends TestCase
{
    public function testInitialValues()
    {
        $accumulator = new ResultAccumulator();
        
        $this->assertSame(0, $accumulator->getTestCount(), 'Initial test count should be 0');
        $this->assertSame(0, $accumulator->getFailCount(), 'Initial fail count should be 0');
        $this->assertSame(0, $accumulator->getAssertionCount(), 'Initial assertion count should be 0');
        $this->assertSame([], $accumulator->getFailedTests(), 'Initial failed tests should be empty array');
        $this->assertSame(false, $accumulator->hasFailures(), 'Initial hasFailures should be false');
    }
    
    public function testIncrementTestCount()
    {
        $accumulator = new ResultAccumulator();
        
        $accumulator->incrementTestCount();
        $this->assertSame(1, $accumulator->getTestCount(), 'Test count should be 1 after increment');
        
        $accumulator->incrementTestCount();
        $this->assertSame(2, $accumulator->getTestCount(), 'Test count should be 2 after second increment');
    }
    
    public function testIncrementFailCount()
    {
        $accumulator = new ResultAccumulator();
        
        $accumulator->incrementFailCount();
        $this->assertSame(1, $accumulator->getFailCount(), 'Fail count should be 1 after increment');
        $this->assertSame(true, $accumulator->hasFailures(), 'hasFailures should be true after fail increment');
        
        $accumulator->incrementFailCount();
        $this->assertSame(2, $accumulator->getFailCount(), 'Fail count should be 2 after second increment');
    }
    
    public function testIncrementAssertionCount()
    {
        $accumulator = new ResultAccumulator();
        
        $accumulator->incrementAssertionCount();
        $this->assertSame(1, $accumulator->getAssertionCount(), 'Assertion count should be 1 after increment');
        
        $accumulator->incrementAssertionCount();
        $this->assertSame(2, $accumulator->getAssertionCount(), 'Assertion count should be 2 after second increment');
    }
    
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
        $this->assertSame(3, $accumulator->getTestCount(), 'Should have 3 tests');
        $this->assertSame(4, $accumulator->getAssertionCount(), 'Should have 4 assertions');
        $this->assertSame(1, $accumulator->getFailCount(), 'Should have 1 failure');
        $this->assertSame(['SomeTest::testFailingMethod'], $accumulator->getFailedTests(), 'Should have 1 failed test');
        $this->assertSame(true, $accumulator->hasFailures(), 'Should have failures');
    }
}
