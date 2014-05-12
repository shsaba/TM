<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

$app->match('/', function () use ($app) {
    return $app->redirect('add-for-categories');
});


$app->match('/add-for-categories', function (Request $request) use ($app) {


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

    return $app['twig']->render('tasksCategories.html.twig', array(
                'form' => $form->createView(),
                'type' => 'categories'));
})->bind('categories');



$app->match('/add-for-kindstasks', function (Request $request) use ($app) {

    $arrayQuery = array();
    $statement = $app['db']->executeQuery('SELECT id,category FROM categories ORDER BY id DESC');
    while ($tipo = $statement->fetch()) {
        $arrayQuery[$tipo['id']] = $tipo['category'];
    }

    if (empty($arrayQuery)) {
        return $app->redirect('main');
    }
    $form = $app['form.factory']->createBuilder('form')
            ->add('kind', 'text', array(
                'attr' => array(
                    'placeholder' => 'Type de tâches',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 5))
                ),
                'label' => 'Type'
            ))
            ->add('category_id', 'choice', array(
                'choices' => $arrayQuery,
                'expanded' => false,
                'label' => 'Catégorie'
            ))
            ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $data = $form->getData();
            $app['db']->insert('kindstasks', array(
                'category_id' => (int) $data['category_id'],
                'kind' => (string) $data['kind'],
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

    return $app['twig']->render('kindsTasks.html.twig', array(
                'form' => $form->createView(),
                'type' => 'kindstasks'));
})->bind('kindstasks');



$app->match('/checking-reports', function (Request $request) use ($app) {

    $date = $request->request->get('dateofreport');

    $sql = 'SELECT * FROM reports WHERE date = ?';
    $report = $app['db']->fetchAll($sql, array(
        $date));

    if ($report != 0) {
        $app['db']->insert('reports', array(
            'date' => $date,
            'note' => ''
        ));
    }

    return $app->redirect('report-' . $date);
})->bind('repports');



$app->match('/report-{date}', function ($date, Request $request) use ($app) {

    $sql = 'SELECT * FROM reports WHERE date = ?';
    $report = $app['db']->fetchAssoc($sql, array(
        $date));

    $form = $app['form.factory']->createBuilder('form')
            ->add('notes', 'textarea', array(
                'attr' => array(
                    'placeholder' => 'Notes',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 5))
                ),
                'label' => 'Notes',
                'data' => $report['note'],
            ))
            ->add('reportId', 'hidden', array(
                'data' => $report['id'],
            ))
            ->getForm();


    $form->handleRequest($request);

    if ($form->isSubmitted()) {

        if ($form->isValid()) {
            $data = $form->getData();

            $response['note'] = $data['notes'];
            $app['db']->update('reports', $response, array(
                'id' => $data['reportId']));

            return true;
        } else {
            foreach ($form as $fieldName => $formField) {
                $errorText = $formField->getErrorsAsString();
                if (!empty($errorText)) {
                    $errorss[$fieldName] = $errorText;
                }
            }
            return json_encode($errorss);
        }
    } else {
        return $app['twig']->render('reports.html.twig', array(
                    'report' => $report,
                    'form' => $form->createView()));
    }
})->bind('make-report');




$app->match('/load-kind-task', function (Request $request) use ($app) {


    $categorie = $request->request->get('categorie');


    $sql = 'SELECT * FROM kindstasks WHERE category_id = ?';
    $report = $app['db']->fetchAll($sql, array(
        (int) $categorie));


    if (!empty($report)) {
        return $app['twig']->render('tables/kindstasksoption.html.twig', array(
                    'kindstasks' => $report));
    } else {
        return false;
    }
})->bind('load-kind-task');






$app->match('/add-task-for-report-{id}', function (Request $request, $id) use ($app) {

    $categories = array();
    $statement = $app['db']->executeQuery('SELECT id,category FROM categories ORDER BY 2 ASC');
    while ($tipo = $statement->fetch()) {
        $categories[$tipo['id']] = $tipo['category'];
    }

    $kindstasks = array();
    $statement2 = $app['db']->executeQuery('SELECT id,kind FROM kindstasks ORDER BY 2 ASC');
    while ($tipo = $statement2->fetch()) {
        $kindstasks[$tipo['id']] = $tipo['kind'];
    }

    $form = $app['form.factory']->createBuilder('form')
            ->add('category_id_report', 'choice', array(
                'choices' => $categories,
                'expanded' => false,
                'label' => 'Catégorie'
            ))
            ->add('kind_task_id_report', 'choice', array(
                'choices' => $kindstasks,
                'expanded' => false,
                'label' => 'Type de tâche',
                'required' => true,
            ))
            ->add('title', 'text', array(
                'attr' => array(
                    'placeholder' => 'Titre de la tâche',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 5))
                ),
                'label' => 'Titre'
            ))
            ->add('content', 'textarea', array(
                'attr' => array(
                    'placeholder' => 'Description de la tâche',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 10))
                ),
                'label' => 'Description'
            ))
            ->add('reportId', 'hidden', array(
                'data' => $id,
            ))
            ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $data = $form->getData();
            $app['db']->insert('tasksreport', array(
                'report_id' => (int) $data['reportId'],
                'kindTask_id' => (int) $data['kind_task_id_report'],
                'title' => (string) $data['title'],
                'content' => (string) $data['content'],
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

    return $app['twig']->render('taskReport.html.twig', array(
                'form' => $form->createView()));
})->bind('add-task-for-report');





$app->match('/get-task-for-report-{id}', function (Request $request, $id) use ($app) {

    $sql = 'SELECT kt.id, title, content, kind, category '
            . 'FROM kindstasks kt, categories ca, tasksreport ta '
            . 'WHERE kt.category_id = ca.id '
            . 'AND ta.kindTask_id=kt.id '
            . 'AND report_id= ? '
            . 'ORDER BY kt.id ';

    $tasksreport = $app['db']->fetchAll($sql, array(
        (int) $id));



    return $app['twig']->render('tables/tasksreport.html.twig', array(
                'tasksreport' => $tasksreport));
});










$app->match('/last-from-categories', function () use ($app) {

    $category = $app['db']->fetchAll('SELECT * FROM categories ORDER BY id DESC LIMIT 1');
    return $app['twig']->render('tables/categories.html.twig', array(
                'categories' => $category));
});


$app->match('/last-from-kindstasks', function () use ($app) {

    $kindstasks = $app['db']->fetchAll('SELECT k.id, kind, category FROM kindstasks k, categories c WHERE c.id=k.category_id ORDER BY id DESC LIMIT 1');
    return $app['twig']->render('tables/kindstasks.html.twig', array(
                'kindstasks' => $kindstasks));
});

$app->match('/last-from-reports', function () use ($app) {

    $reports = $app['db']->fetchAll('SELECT * FROM reports ORDER BY id DESC LIMIT 1');
    return $app['twig']->render('tables/reports.html.twig', array(
                'reports' => $reports));
});

$app->match('/last-from-tasks-reports', function () use ($app) {

    $tasksreport = $app['db']->fetchAll('SELECT ta.id, kt.id, title, content, kind, category '
            . 'FROM kindstasks kt, categories ca, tasksreport ta '
            . 'WHERE kt.category_id = ca.id '
            . 'AND ta.kindTask_id=kt.id '
            . 'ORDER BY ta.id DESC '
            . 'LIMIT 1');
    return $app['twig']->render('tables/tasksreport.html.twig', array(
                'tasksreport' => $tasksreport));
});



$app->match('/get-categories', function () use ($app) {

    $categories = $app['db']->fetchAll('SELECT * FROM categories ORDER BY id DESC');
    return $app['twig']->render('tables/categories.html.twig', array(
                'categories' => $categories));
});

$app->match('/get-kindstasks', function () use ($app) {

    $kindstasks = $app['db']->fetchAll('SELECT k.id, kind, category FROM kindstasks k, categories c WHERE c.id=k.category_id ORDER BY id DESC');
    return $app['twig']->render('tables/kindstasks.html.twig', array(
                'kindstasks' => $kindstasks));
});


$app->match('/get-reports', function () use ($app) {

    $reports = $app['db']->fetchAll('SELECT * FROM reports ORDER BY id DESC');
    return $app['twig']->render('tables/reports.html.twig', array(
                'reports' => $reports));
});



$app->match('/delete/{type}/{id}', function ($type, $id) use ($app) {

    $app['db']->delete((string) $type, array(
        'id' => (int) $id,
    ));
    return true;
});


$app->match('/call-indicator', function (Request $request) use ($app) {

    $message = $request->request->get('messages');
    $type = $request->request->get('type');

    return $app['twig']->render('indicators/fatal_error.html.twig', array(
                'message' => $message,
                'type' => $type));
});

$app->match('/call-confirmation-box', function () use ($app) {
    return $app['twig']->render('indicators/confirmation.html.twig', array());
});

