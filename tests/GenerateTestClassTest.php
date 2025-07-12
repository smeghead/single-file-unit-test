<?php

require_once __DIR__ . '/../single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;

class GenerateTestClassTest extends TestCase {
    
    public function testGenerateTestClassWithClassName() {
        // テストクラス生成機能をテスト
        $className = 'Fuga';
        $expected = '<?php

use Smeghead\SingleFileUnitTest\TestCase;

class FugaTest extends TestCase {
    public function test_1plus2_is_3() {
        $this->assertSame(3, (new Some())->add(1, 2));
    }

    public function test_it_must_throw_exception() {
        $this->expectExceptionMessage("Error occurred");
        (new Some())->error();
    }
}
';
        
        // まだ実装されていないので、この時点では失敗する
        $actual = \Smeghead\SingleFileUnitTest\generateTestClass($className);
        $this->assertSame($expected, $actual);
    }
    
    public function testGenerateTestClassWithoutClassName() {
        // クラス名を指定しない場合はExampleTestを生成
        $expected = '<?php

use Smeghead\SingleFileUnitTest\TestCase;

class ExampleTest extends TestCase {
    public function test_1plus2_is_3() {
        $this->assertSame(3, (new Some())->add(1, 2));
    }

    public function test_it_must_throw_exception() {
        $this->expectExceptionMessage("Error occurred");
        (new Some())->error();
    }
}
';
        
        // まだ実装されていないので、この時点では失敗する
        $actual = \Smeghead\SingleFileUnitTest\generateTestClass();
        $this->assertSame($expected, $actual);
    }
}
