<?php
require 'TestCase.php';

foreach (glob(__DIR__ . '/tests/*.php') as $file) {
    require $file;
}

\Smeghead\SingleFileUnitTest\TestCase::runAll();
