<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Silex\Provider\TranslationServiceProvider;
use Silex\Application\TranslationTrait;

$app->match('/', function (Request $request) use ($app) {


    $data = array(
        'name' => 'tt',
    );

    $form = $app['form.factory']->createBuilder('form')
            ->add(
                    'name', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 5))),
                'label' => 'Catégorie'
            ))
            ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        print_r($data);
        //return $app->redirect(__DIR__.'/web/');
    }

    // display the form
    return $app['twig']->render('tasksCategories.html.twig', array(
                'form' => $form->createView()));
});



$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message, $code);
});