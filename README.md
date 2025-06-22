# single-file-unit-test

PHP 5.6+ から使える、**依存ゼロの単一ファイルテストフレームワーク**です。

## ✨ 特徴
- `require 'TestCase.php'` だけで使える
- PHPUnitと互換性のある構文（一部）
- `assertSame()` と `expectExceptionMessage()` に対応
- `use PHPUnit\Framework\TestCase` に置き換えればPHPUnitへ移行可能

## 🚀 使い方

```php
<?php

require 'TestCase.php';

use Smeghead\SingleFileUnitTest\TestCase;

class MyTest extends TestCase {
    public function testSomething() {
        $this->assertSame(2, 1 + 1);
    }
}

$test = new MyTest();
$test->runTests();
```

## ✅ CI
GitHub Actions を使って PHP 5.6 ～ 8.4 でテスト実行。

## 📄 ライセンス
MIT License
