<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

$app->match('/', function (Request $request) use ($app) {


    // some default data for when the form is displayed the first time
    $data = array(
        'name' => 'Your name',
        'email' => 'Your email',
    );

    $form = $app['form.factory']->createBuilder('form')
            ->add('name', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 5)))
            ))
            ->add('email', 'text', array(
                'constraints' => new Assert\Email()
            ))
            ->add('gender', 'choice', array(
                'choices' => array(
                    1 => 'male',
                    2 => 'female'),
                'expanded' => true,
                'constraints' => new Assert\Choice(array(
                    1,
                    2)),
            ))
            ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        // do something with the data
        // redirect somewhere
        return $app->redirect('/');
    }

    // display the form
    return $app['twig']->render('index.html.twig', array(
                'form' => $form->createView()));
});
