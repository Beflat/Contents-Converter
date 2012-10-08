<?php

use Symfony\Component\ClassLoader\ApcClassLoader;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance
// Change 'sf2' by the prefix you want in order to prevent key conflict with another application
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
