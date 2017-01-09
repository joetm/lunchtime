<?php

/**
 * Linked Data platform
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

error_reporting(-1); // report ALL errors

require __DIR__ . '/vendor/autoload.php';


$app = new \Silex\Application();

$app->get('/ns/{args}', function ($args) use ($app) {
    return 'Hello ' . $app->escape($args);
})
->assert('args', '.*')
->convert('args', function ($args) {
    return explode('/', $args);
});

$app->get('/id/{args}', function ($args) use ($app) {
    return 'Hello ' . $app->escape($args);
})
->assert('args', '.*')
->convert('args', function ($args) {
    return explode('/', $args);
});

$app->run();
