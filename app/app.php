<?php

use Bricks\Objects\Insight;
use Bricks\Objects\Set;
use Bricks\Objects\Shop;
use Bricks\Repositories\EntityManager;
use Bricks\Response\ErrorResponse;
use Bricks\Services\Persist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once 'vendor/autoload.php';

$app = new Silex\Application();

$app['logger'] = function () {
    $logger = new Monolog\Logger('bricks_logger');
    return $logger->pushHandler(
        new Monolog\Handler\StreamHandler(
            __DIR__ . '/logs/my_app.log',
            Monolog\Logger::WARNING
        )
    );
};

$app['response'] = function () {
    return new Bricks\Response\ResponseFactory();
};

$app['entity_manager'] = function () {
    return new EntityManager();
};

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
});

$app->get('/api/v1/stats/', function () use ($app) {
    $response = $app['response'];
    $json = $response->getStatistics();
    $array = $json->asArray();
    return new JsonResponse($array);
});

$app->get('/api/v1/insight/{timestamp}', function ($timestamp) use ($app) {
    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Insights');
    $insightCollection = $bricksRepository->findAll();

    foreach ($insightCollection as $insightItem) {
        $insight = unserialize($insightItem);
        if ($insight->getTimestamp() == $timestamp) {
            $json = $app['response']->getObject($insight);
            return new JsonResponse($json->asArray(), 200);
        }
    }

    return new JsonResponse([
        'status' => 'error',
        'code' => 404,
        'message' => 'No insight found',
    ], 404);
});

$app->get('/api/v1/shop/{slug}', function ($slug) use ($app) {
    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Shops');
    $allShops = $bricksRepository->findAll();

    foreach ($allShops as $shopItem) {
        $shop = unserialize($shopItem);
        if ($shop->getSlug() == $slug) {
            $json = $app['response']->getObject($shop);
            return new JsonResponse($json->asArray(), 200);
        }
    }

    return new JsonResponse([
        'status' => 'error',
        'code' => 404,
        'message' => 'Shop not found',
    ], 404);
});

$app->delete('/api/v1/set/{code}', function ($code) use ($app) {
    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Bricks');
    $allSets = $bricksRepository->findAll();

    $bricksRepository->deleteAll();

    foreach ($allSets as $set) {
        $item = unserialize($set);
        if ($item->get('code') != $code) {
            (new Persist(
                $item,
                new Bricks\Services\NamesGenerator(),
                $app['logger']
            ))->persist();
        }
    }

    return new Response('', 204);
});

$app->get('/api/v1/set/{code}', function ($code) use ($app) {
    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Bricks');
    $allSets = $bricksRepository->findAll();

    foreach ($allSets as $set) {
        $item = unserialize($set);
        if ($item->get('code') == $code) {
            $json = $app['response']->getObject($item);
            return new JsonResponse($json->asArray(), 200);
        }
    }
});

$app->get('/api/v1/', function () use ($app) {
    $json = $app['response']->getHomepage();
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/set/', function () use ($app) {
    $sets = [];
    $links = [];

    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Bricks');
    $allSets = $bricksRepository->findAll();

    foreach ($allSets as $set) {
        $item = unserialize($set);
        $sets[] = $item->jsonSerialize();

        $links[] = [
            'rel' => 'set ' . $item->get('code'),
            'href' => '/set/' . $item->get('code')
        ];
    }


    $json = $app['response']->getCollection('/set/', $sets, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/insight/', function () use ($app) {
    $insights = [];
    $links = [];
    
    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Insights');
    $allInsights = $bricksRepository->findAll();

    foreach ($allInsights as $item) {
        $insight = unserialize($item);
        $insights[] = $insight->jsonSerialize();

        $links[] = ['rel' => 'set ' . $insight->get('set'), 'href' => '/set/' . $insight->get('set')];
        $links[] = ['rel' => 'set ' . $insight->get('shop'), 'href' => '/shop/' . $insight->get('shop')];
        $links[] = ['rel' => 'insight ' . $insight->getTimestamp(), 'href' => '/insight/' . $insight->getTimestamp()];
    }

    $json = $app['response']->getCollection('/insight/', $insights, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/shop/', function () use ($app) {
    $shops = [];
    $links = [];

    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Shops');
    $allShops = $bricksRepository->findAll();

    foreach ($allShops as $item) {
        $shop = unserialize($item);
        $shops[] = $shop->jsonSerialize();
        $links[] = ['rel' => 'shop ' . $shop->getSlug(), 'href' => '/shop/' . $shop->getSlug()];
    }

    $json = $app['response']->getCollection('/shop/', $shops, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->post('/api/v1/set/', function (Request $request) use ($app) {
    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Bricks');
    $allSets = $bricksRepository->findAll();

    foreach ($allSets as $setItem) {
        $setValueObject = unserialize($setItem);
        if ($setValueObject->get('code') == $request->request->get('code')) {
            return new JsonResponse([
                'status' => 'error',
                'code' => 409,
                'message' => 'Set ' . $setValueObject->get('code') . ' already exists',
            ], 409);
        }
    }

    $app['logger']->warning(var_export($request->request, true));

    $setValueObject = Set::box([
        'code' => $request->request->get('code'),
        'name' => $request->request->get('name'),
        'pieces' => $request->request->get('pieces'),
        'update' => new \DateTime('now'),
    ]);

    (new Persist(
        $setValueObject,
        new Bricks\Services\NamesGenerator(),
        $app['logger']
    ))->persist();

    $setAsArray = $setValueObject->jsonSerialize();

    return new JsonResponse($setAsArray, 201);
});

$app->post('/api/v1/shop/', function (Request $request) use ($app) {
    $manager = $app['entity_manager'];
    $bricksRepository = $manager->getRepository('Shops');
    $allShops = $bricksRepository->findAll();

    foreach ($allShops as $item) {
        $shop = unserialize($item);
        if ($shop->get('address') == $request->request->get('address')) {
            return new JsonResponse([
                'status' => 'error',
                'code' => 409,
                'message' => 'Set ' . $shop->get('code') . ' already exists',
            ], 409);
        }
    }

    $shop = Shop::box([
        'name' => $request->request->get('name'),
        'address' => $request->request->get('address'),
        'update' => new \DateTime('now'),
    ]);

    (new Persist(
        $shop,
        new Bricks\Services\NamesGenerator(),
        $app['logger']
    ))->persist();

    $shopAsArray = $shop->jsonSerialize();

    return new JsonResponse($shopAsArray, 201);
});

$app->post('/api/v1/insight/', function (Request $request) use ($app) {
    $insight = Insight::box([
        'shop' => $request->request->get('shop'),
        'set' => $request->request->get('set'),
        'value' => $request->request->get('value'),
        'update' => new \DateTime('now'),
    ]);

    (new Persist(
        $insight,
        new Bricks\Services\NamesGenerator(),
        $app['logger']
    ))->persist();

    $insihtAsArray = $insight->jsonSerialize();

    return new JsonResponse($insihtAsArray, 201);
});

$app->error(function (\Exception $e) use ($app) {
    $app['logger']->warning($e->getMessage());
    $app['logger']->warning($e->getTraceAsString());
    return new JsonResponse(
        ErrorResponse::withMessage('Invalid request')
            ->jsonSerialize(),
        404
    );
});

$app->get('/api/v1/resources/', function (Request $request) use ($app) {

    return new JsonResponse(
        $app['routes']->getIterator(),
        200
    );
});

return $app;
