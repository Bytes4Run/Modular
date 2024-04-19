#!/usr/bin/env php
<?php
/**
 * Created by Bytes4Run.
 * User: jorge Echeverria
 * Date: 24-04-16
 * Time: 5:20 PM
 */
require_once __DIR__ . '/vendor/autoload.php';
//require_once __DIR__ . 'app/core/libraries/ClassBuilder.php';
use Kernel\libraries\ClassBuilder;
$app = new ClassBuilder;
if (isset($argv[1]) && !empty($argv)) {
    if ($argv[1] == "help" || $argv[1] == "--help" || $argv[1] == "-h") {
        echo $app->help();
    } else {
        echo $app->build($argv);
    }
    exit(0);
}
?>