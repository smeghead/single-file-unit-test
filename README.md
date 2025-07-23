# single-file-unit-test

A **zero-dependency, single-file unit testing framework** for PHP 5.6 and above.  
Just `require_once 'single-file-unit-test.php'` and you're ready to start testing, with a design that facilitates migration to PHPUnit.

---

## Why was this created?

This framework was born from the experience of supporting **legacy PHP projects in crisis**.

- No `composer` setup
- No framework, or custom legacy implementations
- Zero test code, making it difficult to set up a testing environment

This tool was created to meet the real-world need of "**I just want to write my first test**" in such situations.

---

## Features

- Works with just `require_once 'single-file-unit-test.php'`
- Supports `assertSame` and `expectExceptionMessage`
- PHPUnit-compatible `TestCase` inheritance (easy migration to PHPUnit later)
- CLI execution with `php single-file-unit-test.php tests/`
- `--help` and `--version` options for help and version display
- Exit codes for success/failure determination (CI compatible)
- PHP 5.6 to 8.4 support (GitHub Actions ready)

---

## Install

Installation is **incredibly simple** - just download the single file to your project:

```bash
curl -o single-file-unit-test.php https://raw.githubusercontent.com/smeghead/single-file-unit-test/main/single-file-unit-test.php
```

That's it! No `composer install`, no complex setup, no dependencies to manage.  
Just one command and you're ready to start testing.

---

## Usage

### A. Using as a Library

```php
<?php

require_once 'single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;

class MyTest extends TestCase {
    public function testSomething() {
        $this->assertSame(2, 1 + 1);
    }
}

// Run a single test class
(new MyTest())->runTests();

// Display results (optional)
TestCase::showResults();
```

### B. Using as a CLI Test Runner

```bash
# Run tests
php single-file-unit-test.php tests/

# Show help
php single-file-unit-test.php --help

# Show version
php single-file-unit-test.php --version
```

**Options:**
- `-h, --help`: Display help message
- `-v, --version`: Display version information
- `--generate-test-class=ClassName`: Generate a test class template

**Behavior:**
- Recursively searches the `tests/` directory and loads `*Test.php` files
- Exits with `exit(1)` if any test fails (CI compatible)

### C. Generate Test Class Template

```bash
# Generate a test class template
php single-file-unit-test.php --generate-test-class=Fuga

# Generate ExampleTest when no class name is specified
php single-file-unit-test.php --generate-test-class

# Save to a file
php single-file-unit-test.php --generate-test-class=Fuga > tests/FugaTest.php
```

**Generated template:**
```php
<?php

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
```

This feature helps beginners get started quickly by providing a working test template that they can modify for their specific needs.

---

## Example Test

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

![example](docs/example.jpg)

---

## License

MIT License  
Copyright (c) 2025 smeghead
