<?php
use Smeghead\SingleFileUnitTest\ColorSupport;
use Smeghead\SingleFileUnitTest\TerminalString;
use Smeghead\SingleFileUnitTest\TestCase;

// テスト用のColorSupportクラス（継承してisSupported()をオーバーライド）
class TestColorSupport extends ColorSupport
{
    private $supported;
    
    public function __construct($supported = true)
    {
        $this->supported = $supported;
    }
    
    public function isSupported()
    {
        return $this->supported;
    }
}

class TerminalStringTest extends TestCase
{
    public function testPlainTextWhenNoColorSupport()
    {
        $colorSupport = new TestColorSupport(false); // サポートなし
        $ts = new TerminalString($colorSupport);
        $this->assertSame('hello', $ts->text('hello', 'red', 'green'));
    }

    public function testColorTextWhenSupported()
    {
        $colorSupport = new TestColorSupport(true); // サポートあり
        $ts = new TerminalString($colorSupport);
        $expected = "\033[31;42mhello\033[0m";
        $this->assertSame($expected, $ts->text('hello', 'red', 'green'));
    }

    public function testOnlyFgColor()
    {
        $colorSupport = new TestColorSupport(true); // サポートあり
        $ts = new TerminalString($colorSupport);
        $expected = "\033[31mhello\033[0m";
        $this->assertSame($expected, $ts->text('hello', 'red'));
    }

    public function testOnlyBgColor()
    {
        $colorSupport = new TestColorSupport(true); // サポートあり
        $ts = new TerminalString($colorSupport);
        $expected = "\033[42mhello\033[0m";
        $this->assertSame($expected, $ts->text('hello', null, 'green'));
    }
}
