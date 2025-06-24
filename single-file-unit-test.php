<?php
namespace Smeghead\SingleFileUnitTest;

const VERSION = 'v0.0.0';

class ColorSupport
{
    /**
     * ターミナルが色をサポートしているかどうかを判定する
     * @return bool 色をサポートしている場合はtrue
     */
    public function isSupported()
    {
        // NO_COLOR環境変数が設定されている場合は色を無効にする
        if (getenv('NO_COLOR') !== false) {
            return false;
        }

        // TTYでない場合（リダイレクトやパイプされている場合）は色を無効にする
        if (function_exists('posix_isatty') && !posix_isatty(STDOUT)) {
            return false;
        }

        // TERM環境変数をチェック
        $term = getenv('TERM');
        if ($term === false || $term === 'dumb') {
            return false;
        }

        // 色をサポートすることが知られているTERM値
        $colorTerms = array(
            'xterm', 'xterm-color', 'xterm-256color',
            'screen', 'screen-256color',
            'tmux', 'tmux-256color',
            'rxvt', 'rxvt-unicode', 'rxvt-256color',
            'linux', 'cygwin',
            'ansi', 'vt100', 'vt220'
        );

        foreach ($colorTerms as $colorTerm) {
            if (strpos($term, $colorTerm) === 0) {
                return true;
            }
        }

        // COLORTERM環境変数が設定されている場合
        if (getenv('COLORTERM') !== false) {
            return true;
        }

        // デフォルトでは色をサポートしないと仮定
        return false;
    }

    /**
     * NO_COLOR環境変数が設定されているかチェック
     * @return bool NO_COLOR環境変数が設定されている場合はtrue
     */
    public function isNoColorSet()
    {
        return getenv('NO_COLOR') !== false;
    }

    /**
     * 出力先がTTYかどうかをチェック
     * @return bool TTYの場合はtrue
     */
    public function isTty()
    {
        return function_exists('posix_isatty') && posix_isatty(STDOUT);
    }

    /**
     * TERM環境変数の値を取得
     * @return string|false TERM環境変数の値、設定されていない場合はfalse
     */
    public function getTerm()
    {
        return getenv('TERM');
    }

    /**
     * COLORTERM環境変数が設定されているかチェック
     * @return bool COLORTERM環境変数が設定されている場合はtrue
     */
    public function isColorTermSet()
    {
        return getenv('COLORTERM') !== false;
    }
}

final class TerminalString
{
    private $colorSupport;
    private static $fgColors = [
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'white' => '97',
    ];
    private static $bgColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'white' => '107',
    ];

    public function __construct(ColorSupport $colorSupport)
    {
        $this->colorSupport = $colorSupport;
    }

    public function text($text, $fg = null, $bg = null)
    {
        if (PHP_SAPI === 'cli' && $this->colorSupport->isSupported() && ($fg || $bg)) {
            $codes = [];
            if ($fg && isset(self::$fgColors[$fg])) {
                $codes[] = self::$fgColors[$fg];
            }
            if ($bg && isset(self::$bgColors[$bg])) {
                $codes[] = self::$bgColors[$bg];
            }
            if ($codes) {
                return "\033[" . implode(';', $codes) . "m" . $text . "\033[0m";
            }
        }
        return $text;
    }
}

final class ResultAccumulator
{
    private $testCount = 0;
    private $failCount = 0;
    private $assertionCount = 0;
    private $failedTests = [];

    public function incrementTestCount()
    {
        $this->testCount++;
    }

    public function incrementFailCount()
    {
        $this->failCount++;
    }

    public function incrementAssertionCount()
    {
        $this->assertionCount++;
    }

    public function addFailedTest($testName)
    {
        $this->failedTests[] = $testName;
    }

    public function getTestCount()
    {
        return $this->testCount;
    }

    public function getFailCount()
    {
        return $this->failCount;
    }

    public function getAssertionCount()
    {
        return $this->assertionCount;
    }

    public function getFailedTests()
    {
        return $this->failedTests;
    }

    public function hasFailures()
    {
        return $this->failCount > 0;
    }
}

class TestCase
{
    private $expectedExceptionMessage = null;
    private static $resultAccumulator = null;
    private $colorSupport;
    private $terminalString;

    public function __construct()
    {
        $this->colorSupport = new ColorSupport();
        $this->terminalString = new TerminalString($this->colorSupport);
    }

    public function expectExceptionMessage($message)
    {
        $this->expectedExceptionMessage = $message;
    }

    public function assertSame($expected, $actual, $message = '')
    {
        self::$resultAccumulator->incrementAssertionCount();
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

            self::$resultAccumulator->incrementTestCount();
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
                if ($this->expectedExceptionMessage !== null &&
                    strpos($e->getMessage(), $this->expectedExceptionMessage) !== false) {
                    $this->out("✔ $class::$method (expected exception caught)", 'green');
                } else {
                    self::$resultAccumulator->incrementFailCount();
                    self::$resultAccumulator->addFailedTest("$class::$method");
                    $this->out("✘ $class::$method", 'red');
                    $this->out("   " . $e->getMessage(), 'yellow');
                }
            }
        }
    }

    private function out($text, $color = null)
    {
        echo $this->terminalString->text($text, $color) . "\n";
    }

    public static function runAll()
    {
        self::$resultAccumulator = new ResultAccumulator();
        
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, __CLASS__)) {
                $instance = new $class();
                $instance->runTests();
            }
        }

        // Summary output
        $colorSupport = new ColorSupport();
        $terminalString = new TerminalString($colorSupport);
        echo "\n"; // 空行を追加
        if (!self::$resultAccumulator->hasFailures()) {
            echo $terminalString->text(
                "OK (" . self::$resultAccumulator->getTestCount() . " tests, " . self::$resultAccumulator->getAssertionCount() . " assertions)",
                'black', 'green'
            ) . "\n";
        } else {
            echo $terminalString->text("FAILURES!", 'white', 'red') . "\n";
            echo "Tests: " . self::$resultAccumulator->getTestCount() . " Assertions: " . self::$resultAccumulator->getAssertionCount() . " Failures: " . self::$resultAccumulator->getFailCount() . ".\n";
            foreach (self::$resultAccumulator->getFailedTests() as $failTest) {
                echo "  - $failTest\n";
            }
        }

        exit(self::$resultAccumulator->hasFailures() ? 1 : 0);
    }
}

// ---- CLI entry point ----
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['argv'][0])) {
    function showHelpTo($output) {
        $helpText = "Single File Unit Test " . \Smeghead\SingleFileUnitTest\VERSION . "\n" .
                    "\n" .
                    "Usage:\n" .
                    "  php single-file-unit-test.php <test_dir_or_file> [...more]\n" .
                    "  php single-file-unit-test.php -h|--help\n" .
                    "  php single-file-unit-test.php -v|--version\n" .
                    "\n" .
                    "Options:\n" .
                    "  -h, --help     Show this help message\n" .
                    "  -v, --version  Show version information\n" .
                    "\n" .
                    "Arguments:\n" .
                    "  test_dir_or_file  Path to test directory or test file\n";
        fwrite($output, $helpText);
    }

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
    
    // ヘルプ表示の処理
    if (!empty($args) && (in_array('-h', $args) || in_array('--help', $args))) {
        showHelpTo(STDOUT);
        exit(0);
    }
    
    // バージョン表示の処理
    if (!empty($args) && (in_array('-v', $args) || in_array('--version', $args))) {
        echo "Single File Unit Test " . \Smeghead\SingleFileUnitTest\VERSION . "\n";
        exit(0);
    }
    
    if (empty($args)) {
        showHelpTo(STDERR);
        exit(2);
    }

    foreach ($args as $arg) {
        loadTestFiles($arg);
    }

    \Smeghead\SingleFileUnitTest\TestCase::runAll();
}
