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
            $sql = "INSERT INTO `categories`(`category`) VALUES (?)";
            $app['db']->executeUpdate($sql, array(
                (string) $data['name']));

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

    $categories = $app['db']->fetchAll('SELECT * FROM categories ORDER BY id');
    return $app['twig']->render('tables/category.html.twig', array(
                'categories' => $categories));
});

