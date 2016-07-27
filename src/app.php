<?php
namespace OIDCWebClient;

require_once __DIR__.'/../vendor/autoload.php';

$app = new \Silex\Application();

// ... definitions
$app['debug'] = true;

$app->get('/hello/{name}', function($name) use($app) {
	    return 'Hello '.$app->escape($name);
});

$app->mount('/', new HelloControllerProvider());

return $app;
