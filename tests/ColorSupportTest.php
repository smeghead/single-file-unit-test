<?php

require_once __DIR__ . '/../single-file-unit-test.php';

use Smeghead\SingleFileUnitTest\TestCase;
use Smeghead\SingleFileUnitTest\ColorSupport;

class ColorSupportTest extends TestCase
{
    public function testIsSupportedReturnsTrueInColorTerminal()
    {
        $colorSupport = new ColorSupport();
        
        // 現在の環境でのテスト
        // 通常のターミナルではtrueになることを確認
        if (function_exists('posix_isatty') && posix_isatty(STDOUT) && getenv('NO_COLOR') === false) {
            $this->assertSame(true, $colorSupport->isSupported(), 'Color should be supported in a normal terminal');
        }
    }

    public function testIsSupportedReturnsFalseWhenNoColorIsSet()
    {
        $colorSupport = new ColorSupport();
        
        // NO_COLOR環境変数を一時的に設定してテスト
        $originalNoColor = getenv('NO_COLOR');
        putenv('NO_COLOR=1');
        
        $this->assertSame(false, $colorSupport->isSupported(), 'Color should be disabled when NO_COLOR is set');
        
        // 元の状態に戻す
        if ($originalNoColor === false) {
            putenv('NO_COLOR');
        } else {
            putenv("NO_COLOR=$originalNoColor");
        }
    }

    public function testIsSupportedWithDumbTerm()
    {
        $colorSupport = new ColorSupport();
        
        // TERM環境変数を一時的に'dumb'に設定してテスト
        $originalTerm = getenv('TERM');
        putenv('TERM=dumb');
        
        $this->assertSame(false, $colorSupport->isSupported(), 'Color should be disabled when TERM is "dumb"');
        
        // 元の状態に戻す
        if ($originalTerm === false) {
            putenv('TERM');
        } else {
            putenv("TERM=$originalTerm");
        }
    }

    public function testIsSupportedWithXtermTerm()
    {
        $colorSupport = new ColorSupport();
        
        // TERM環境変数を一時的に'xterm'に設定してテスト
        $originalTerm = getenv('TERM');
        $originalNoColor = getenv('NO_COLOR');
        
        putenv('TERM=xterm');
        putenv('NO_COLOR'); // NO_COLORを削除
        
        // TTYの場合のみテスト
        if (function_exists('posix_isatty') && posix_isatty(STDOUT)) {
            $this->assertSame(true, $colorSupport->isSupported(), 'Color should be supported when TERM is "xterm"');
        }
        
        // 元の状態に戻す
        if ($originalTerm === false) {
            putenv('TERM');
        } else {
            putenv("TERM=$originalTerm");
        }
        if ($originalNoColor !== false) {
            putenv("NO_COLOR=$originalNoColor");
        }
    }
}
