# single-file-unit-test

PHP 5.6 以上で動作する、依存ゼロの**シングルファイル・ユニットテストフレームワーク**です。  
`require 'single-file-unit-test.php'` するだけで使い始められ、PHPUnitへの移行も視野に入れた設計です。

---

## 🔥 なぜこれを作ったのか？

これは、**地獄のような炎上PHPプロジェクト**をサポートした経験から生まれました。

- `composer` すら導入されていない
- フレームワークなし、あるいは独自のレガシー実装
- テストコードゼロ、まず書くための環境整備が難しい

このような状況で、「**とにかく最初のテストを1本書きたい**」という現場ニーズに応えるために作られました。

---

## ✅ 特徴

- ✅ `require 'single-file-unit-test.php'` だけで動作
- ✅ `assertSame` と `expectExceptionMessage` に対応
- ✅ PHPUnit 互換の `TestCase` を継承した記述が可能（後からPHPUnitへ移行しやすい）
- ✅ `php single-file-unit-test.php tests/` で CLI 実行可能
- ✅ 終了コードによる成功・失敗判定（CIに対応）
- ✅ PHP 5.6 ～ 8.4 対応（GitHub Actions 対応済み）

---

## 🚀 使い方

### A. ライブラリとして使う

```php
<?php

require 'single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;

class MyTest extends TestCase {
    public function testSomething() {
        $this->assertSame(2, 1 + 1);
    }
}

(new MyTest())->runTests();
```

### B. CLI テストランナーとして使う

```bash
php single-file-unit-test.php tests/
```

- `tests/` ディレクトリを再帰的に探索し `*Test.php` ファイルを読み込みます
- テストが1つでも失敗すれば `exit(1)` で終了します（CI対応）

---

## 🧪 サンプルテスト

```php
<?php

use Smeghead\SingleFileUnitTest\TestCase;

class Some {
    public function add($a, $b) { return $a + $b; }
    public function error() { throw new Exception("Error occurred"); }
}

class SomeTest extends TestCase {
    public function testAdd() {
        $this->assertSame(3, (new Some())->add(1, 2));
    }

    public function testThrows() {
        $this->expectExceptionMessage("Error occurred");
        (new Some())->error();
    }
}
```

---

## 📄 ライセンス

MIT License  
Copyright (c) 2025 smeghead
