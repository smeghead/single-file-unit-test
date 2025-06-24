<?php
use Smeghead\SingleFileUnitTest\ColorSupport;
use Smeghead\SingleFileUnitTest\TerminalString;
use Smeghead\SingleFileUnitTest\TestCase;

class TerminalStringTest extends TestCase
{
    public function testPlainTextWhenNoColorSupport()
    {
        $old = getenv('NO_COLOR');
        putenv('NO_COLOR=1');
        $colorSupport = new ColorSupport();
        $ts = new TerminalString($colorSupport);
        $this->assertSame('hello', $ts->text('hello', 'red', 'green'));
        if ($old === false) {
            putenv('NO_COLOR'); // unset
        } else {
            putenv('NO_COLOR=' . $old);
        }
    }

    public function testColorTextWhenSupported()
    {
        $old = getenv('NO_COLOR');
        putenv('NO_COLOR'); // unset
        $colorSupport = new ColorSupport();
        $ts = new TerminalString($colorSupport);
        $expected = "\033[31;42mhello\033[0m";
        $this->assertSame($expected, $ts->text('hello', 'red', 'green'));
        if ($old !== false) {
            putenv('NO_COLOR=' . $old);
        }
    }

    public function testOnlyFgColor()
    {
        $old = getenv('NO_COLOR');
        putenv('NO_COLOR');
        $colorSupport = new ColorSupport();
        $ts = new TerminalString($colorSupport);
        $expected = "\033[31mhello\033[0m";
        $this->assertSame($expected, $ts->text('hello', 'red'));
        if ($old !== false) {
            putenv('NO_COLOR=' . $old);
        }
    }

    public function testOnlyBgColor()
    {
        $old = getenv('NO_COLOR');
        putenv('NO_COLOR');
        $colorSupport = new ColorSupport();
        $ts = new TerminalString($colorSupport);
        $expected = "\033[42mhello\033[0m";
        $this->assertSame($expected, $ts->text('hello', null, 'green'));
        if ($old !== false) {
            putenv('NO_COLOR=' . $old);
        }
    }
}
