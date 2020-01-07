#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use YoastDocParser\Commands\ParseCommand;

ini_set( 'xdebug.max_nesting_level', 3000 );

$application = new Application( 'Yoast Parser', '1.0.0' );
$application->add( new ParseCommand() );
$application->run();
