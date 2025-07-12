<?php

require_once __DIR__ . '/../single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;

class CliArgumentParsingTest extends TestCase {
    
    public function testParseGenerateTestClassOption() {
        // --generate-test-class=Fuga の解析をテスト
        $args = ['--generate-test-class=Fuga'];
        $result = \Smeghead\SingleFileUnitTest\parseGenerateTestClassOption($args);
        
        $this->assertSame('Fuga', $result, '--generate-test-class=Fuga should return "Fuga"');
    }
    
    public function testParseGenerateTestClassOptionWithoutValue() {
        // --generate-test-class の解析をテスト（値なし）
        $args = ['--generate-test-class'];
        $result = \Smeghead\SingleFileUnitTest\parseGenerateTestClassOption($args);
        
        $this->assertSame('Example', $result, '--generate-test-class should return "Example"');
    }
    
    public function testParseGenerateTestClassOptionNotPresent() {
        // --generate-test-class が存在しない場合
        $args = ['-h'];
        $result = \Smeghead\SingleFileUnitTest\parseGenerateTestClassOption($args);
        
        $this->assertSame(null, $result, 'Should return null when option is not present');
    }
    
    public function testCliExecutionWithGenerateTestClass() {
        // CLI実行時の--generate-test-classオプションのテスト
        // 実際の出力を検証するのは複雑なので、処理が正しく分岐することをテスト
        $args = ['--generate-test-class=TestClass'];
        $shouldGenerate = \Smeghead\SingleFileUnitTest\parseGenerateTestClassOption($args) !== null;
        
        $this->assertSame(true, $shouldGenerate, 'Should recognize generate test class option');
    }
}
