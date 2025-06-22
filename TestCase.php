<?php
namespace Smeghead\SingleFileUnitTest;

class TestCase
{
    private $expectedExceptionMessage = null;
    private static $failCount = 0;

    public function expectExceptionMessage($message)
    {
        $this->expectedExceptionMessage = $message;
    }

    public function assertSame($expected, $actual, $message = '')
    {
        if ($expected !== $actual) {
            throw new \Exception(
                $message . "\nExpected: " . var_export($expected, true) .
                "\nActual  : " . var_export($actual, true)
            );
        }
    }

    public function runTests()
    {
        $class = get_class($this);
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (strpos($method, 'test') !== 0) continue;

            $this->expectedExceptionMessage = null;

            try {
                ob_start();
                $this->$method();
                ob_end_clean();

                if ($this->expectedExceptionMessage !== null) {
                    throw new \Exception("Failed asserting that exception message [{$this->expectedExceptionMessage}] was thrown.");
                }

                $this->out("✔ $class::$method", 'green');
            } catch (\Exception $e) {
                self::$failCount++;
                if ($this->expectedExceptionMessage !== null &&
                    strpos($e->getMessage(), $this->expectedExceptionMessage) !== false) {
                    $this->out("✔ $class::$method (expected exception caught)", 'green');
                } else {
                    $this->out("✘ $class::$method", 'red');
                    $this->out("   " . $e->getMessage(), 'yellow');
                }
            }
        }
    }

    private function out($text, $color = null)
    {
        $colors = array(
            'red' => '0;31',
            'green' => '0;32',
            'yellow' => '1;33',
        );

        if (PHP_SAPI === 'cli' && isset($colors[$color])) {
            echo "\033[" . $colors[$color] . "m" . $text . "\033[0m\n";
        } else {
            echo $text . "\n";
        }
    }

    public static function runAll()
    {
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, __CLASS__)) {
                $instance = new $class();
                $instance->runTests();
            }
        }

        exit(self::$failCount > 0 ? 1 : 0);
    }
}

// ---- CLI entry point ----
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['argv'][0])) {
    function loadTestFiles($path) {
        if (is_dir($path)) {
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($rii as $file) {
                if ($file->isFile() && preg_match('/Test\.php$/', $file->getFilename())) {
                    require $file->getPathname();
                }
            }
        } elseif (is_file($path)) {
            require $path;
        } else {
            fwrite(STDERR, "Invalid path: $path\n");
            exit(2);
        }
    }

    $args = array_slice($_SERVER['argv'], 1);
    if (empty($args)) {
        fwrite(STDERR, "Usage: php TestCase.php <test_dir_or_file> [...more]\n");
        exit(2);
    }

    foreach ($args as $arg) {
        loadTestFiles($arg);
    }

    \Smeghead\SingleFileUnitTest\TestCase::runAll();
}
