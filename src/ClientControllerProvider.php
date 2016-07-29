<?php
namespace OIDCWebClient;

use \Symfony\Component\Form\Extension\Core\Type\FormType;
use \Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use \Silex\Application;
use \Silex\Api\ControllerProviderInterface;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class ClientControllerProvider implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    ini_set('CURLOPT_ACCEPTTIMEOUT_MS', 90000);
    // creates a new controller based on the default route
    $controllers = $app['controllers_factory'];

    $controllers->match('/client', function (Request $request) use ($app) {
      // some default data for when the form is displayed the first time
      $data = array(
        'client_name' => 'my-lc',
        'url' => 'http://alpha.id.cultura.gov.br/app.php/',
        'redirect_url' => 'http://oidc-web-client.programador-independente.xyz/'
      );

      $form = $app['form.factory']->createBuilder(FormType::class, $data)
      ->add('client_name')
      ->add('url')
      ->add('redirect_url')
      ->getForm();

      $form->handleRequest($request);

      if ($form->isValid()) {
        $data = $form->getData();

        $oidc = new \OpenIDConnectClient($data['url']);
        $oidc->setRedirectURL($data['redirect_url']);
        $oidc->setProviderURL($data['url']);
        $oidc->setClientName($data['client_name']);

        $oidc->register();
        $data['client_id'] = $oidc->getClientID();
        $data['client_secret'] = $oidc->getClientSecret();

        $app['session']->set('client', $data);
        $app['monolog']->addDebug($data);
      }

      // display the form
      return $app['twig']->render('client.twig', array('form' => $form->createView()));
    });

    $controllers->match('/authenticate', function (Request $request) use ($app) {
      // some default data for when the form is displayed the first time
      $data = $app['session']->get('client');

      $form = $app['form.factory']->createBuilder(FormType::class, $data)
      ->add('url')
      ->add('client_id')
      ->add('client_secret')
      ->add('scopes')
      ->add('redirect_url')
      ->getForm();

      $form->handleRequest($request);

      if ($form->isValid()) {
        $data = $form->getData();
        $app['session']->set('client', $data);
        $oidc = new \OpenIDConnectClient($data['url'],
                                        $data['client_id'],
                                        $data['client_secret']);
        $oidc->setRedirectURL($data['redirect_url']);
        $oidc->setProviderURL($data['url']);
        $oidc->authenticate();
        $name = $oidc->requestUserInfo($data['scopes']);

        $app['monolog']->addDebug($name);
      }

      // display the form
      return $app['twig']->render('authenticate.twig', array('form' => $form->createView()));
    });

		$controllers->match('/callback', function (Request $request) use ($app) {
      $data = $app['session']->get('client');

      $oidc   = new \OpenIDConnectClient($data['url'],
                                      $data['client_id'],
                                      $data['client_secret']);
      $oidc->setRedirectURL($data['redirect_url']);
      $oidc->setProviderURL($data['url']);
      $oidc->authenticate();
      // $app['session']->set('accessToken', $oidc->accessToken);
      $oidc->addScope(explode(',', $data['scopes']));
      $name = $oidc->requestUserInfo($data['scopes']);

      // $app['monolog']->addDebug(print_r()$data);

      // display the form
      return $app['twig']->render('callback.twig', array('data' => $name));
    });


		$controllers->match('/', function (Request $request) use ($app) {
      // display the form
      return $app['twig']->render('home.twig');
    });

    return $controllers;
  }
}
