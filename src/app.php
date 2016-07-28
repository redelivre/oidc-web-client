<?php
namespace OIDCWebClient;

require_once __DIR__.'/../vendor/autoload.php';

// use Silex\Provider\FormServiceProvider;

$app = new \Silex\Application();

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/development.log',
));

$app->register(new \Silex\Provider\LocaleServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app->register(new \Silex\Provider\FormServiceProvider());

$app->register(new \Silex\Provider\ValidatorServiceProvider());

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Views',
));

$app['debug'] = true;

$app->get('/hello/{name}', function($name) use($app) {
  return $app['twig']->render('hello.twig', array(
    'name' => $name,
  ));

});

$app->mount('/', new ClientControllerProvider());

return $app;
