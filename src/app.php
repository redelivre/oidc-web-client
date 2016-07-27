<?php
namespace OIDCWebClient;

require_once __DIR__.'/../vendor/autoload.php';

$app = new \Silex\Application();

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/development.log',
));

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Views',
));

$app['debug'] = true;

$app->get('/hello/{name}', function($name) use($app) {
  return $app['twig']->render('hello.twig', array(
    'name' => $name,
  ));

});

$app->mount('/', new HelloControllerProvider());

return $app;
