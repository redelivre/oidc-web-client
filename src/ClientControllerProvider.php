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

    $controllers->match('/', function (Request $request) use ($app) {
      // some default data for when the form is displayed the first time
      $data = array(
        'client_name' => 'Name',
				'url' => 'OpenID URL',
        'redirect_url' => 'Redirect URL'
      );

      $form = $app['form.factory']->createBuilder(FormType::class, $data)
      ->add('client_name')
      ->add('url')
			->add('redirect_url')
      // ->add('billing_plan', ChoiceType::class, array(
      //   'choices' => array(1 => 'free', 2 => 'small_business', 3 => 'corporate'),
      //   'expanded' => true,
      // ))
      ->getForm();

      $form->handleRequest($request);

      if ($form->isValid()) {
        $data = $form->getData();

        $oidc = new \OpenIDConnectClient($data['url']);
				$oidc->setRedirectURL($data['redirect_url']);
				$oidc->setProviderURL($data['url']);
				$oidc->setClientName($data['client_name']);
        $oidc->register();
        $client_id = $oidc->getClientID();
        // $client_secret = $oidc->getClientSecret();

        $app['monolog']->addDebug($client_id);

        // do something with the data

        // redirect somewhere
        // return $app->redirect('...');
      }

      // display the form
      return $app['twig']->render('client/index.twig', array('form' => $form->createView()));
    });

    return $controllers;
  }
}
