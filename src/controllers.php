<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

$app->match('/', function (Request $request) use ($app) {


    $form = $app['form.factory']->createBuilder('form')
            ->add('name', 'text', array(
                'attr' => array(
                    'placeholder' => 'Nom de la catégorie',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 5))
                ),
                'label' => 'Catégorie'
            ))
            ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $data = $form->getData();
            $app['db']->insert('categories', array(
                'category' => (string) $data['name'],
            ));

            return new Response(json_encode(array()));
        } else {
            foreach ($form as $fieldName => $formField) {
                $errorText = $formField->getErrorsAsString();
                if (!empty($errorText)) {
                    $errorss[$fieldName] = $errorText;
                }
            }

            return json_encode($errorss);
        }
    }

    // display the form
    return $app['twig']->render('tasksCategories.html.twig', array(
                'form' => $form->createView()));
});


$app->match('/get-last-category', function () use ($app) {

    $category = $app['db']->fetchAll('SELECT * FROM categories ORDER BY id DESC LIMIT 1');
    return $app['twig']->render('tables/category.html.twig', array(
                'categories' => $category));
});

$app->match('/get-categories', function () use ($app) {

    $categories = $app['db']->fetchAll('SELECT * FROM categories ORDER BY id DESC');
    return $app['twig']->render('tables/category.html.twig', array(
                'categories' => $categories));
});

$app->match('/delete-category-{id}', function ($id) use ($app) {

    $app['db']->delete('categories', array(
        'id' => (int) $id,
    ));

    return $id;
});



$app->match('/call-indicator', function (Request $request) use ($app) {


    $message = $request->request->get('messages');
    $type = $request->request->get('type');

    return $app['twig']->render('indicators/fatal_error.html.twig', array(
                'message' => $message,
                'type' => $type));
});
