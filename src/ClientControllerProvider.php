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
    // creates a new controller based on the default route
    $controllers = $app['controllers_factory'];

    $controllers->match('/', function (Request $request) use ($app) {
      // some default data for when the form is displayed the first time
      $data = array(
        'name' => 'Your name',
        'email' => 'Your email',
      );

      $form = $app['form.factory']->createBuilder(FormType::class, $data)
      ->add('name')
      ->add('email')
      ->add('billing_plan', ChoiceType::class, array(
        'choices' => array(1 => 'free', 2 => 'small_business', 3 => 'corporate'),
        'expanded' => true,
      ))
      ->getForm();

      $form->handleRequest($request);

      if ($form->isValid()) {
        $data = $form->getData();
				$app['monolog']->addDebug($data);

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
