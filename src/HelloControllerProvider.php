<?php
namespace OIDCWebClient;

use \Silex\Application;
use \Silex\Api\ControllerProviderInterface;

class HelloControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app->redirect('/hello/lucas');
        });

        return $controllers;
    }
}
