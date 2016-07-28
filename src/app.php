<?php
namespace OIDCWebClient;

require_once __DIR__.'/../vendor/autoload.php';

$app = new \Silex\Application();
$app['debug'] = true;

$app->register(new \Silex\Provider\LocaleServiceProvider());
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\SessionServiceProvider());

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/development.log',
));

$app->register(new \Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Views',
));

$app->mount('/', new ClientControllerProvider());

return $app;
