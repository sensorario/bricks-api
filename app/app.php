<?php

use Bricks\Objects\Insight;
use Bricks\Objects\Set;
use Bricks\Objects\Shop;
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
    return new Bricks\Factories\ResponseFactory();
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
    $json = $app['response']->getStatistics();
    return new JsonResponse($json->asArray());
});

$app->get('/api/v1/insight/{timestamp}', function ($timestamp) use ($app) {
    /** @todo move outside this path */
    $insightCollection = file('app/data/bricks.objects.insight');
    foreach ($insightCollection as $insightItem) {
        $insight = unserialize($insightItem);
        if ($insight->getTimestamp() == $timestamp) {
            $json = $app['response']->getObject($insight);
            return new JsonResponse($json->asArray(), 200);
        }
    }

    /** @todo define an error table with api error code */
    return new JsonResponse([
        'status' => 'error',
        'code' => 404,
        'message' => 'No insight found',
    ], 404);
});

$app->get('/api/v1/shop/{slug}', function ($slug) use ($app) {
    /** @todo move this path outside from here */
    $allShops = file('app/data/bricks.objects.shop');
    foreach ($allShops as $shopItem) {
        $shop = unserialize($shopItem);
        if ($shop->getSlug() == $slug) {
            $json = $app['response']->getObject($shop);
            return new JsonResponse($json->asArray(), 200);
        }
    }

    /** @todo define an error table with api error code */
    return new JsonResponse([
        'status' => 'error',
        'code' => 404,
        'message' => 'Shop not found',
    ], 404);
});

$app->delete('/api/v1/set/{code}', function ($code) use ($app) {
    $allSets = file('app/data/bricks.objects.set');
    unlink('app/data/bricks.objects.set');
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
    $allSets = file('app/data/bricks.objects.set');
    foreach ($allSets as $set) {
        $item = unserialize($set);
        if ($item->get('code') == $code) {
            $json = $app['response']->getObject($item);
            return new JsonResponse($json->asArray(), 200);
        }
    }
});

$app->get('/api/v1/homepage/', function () use ($app) {
    $json = $app['response']->getHomepage();
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/sets/', function () use ($app) {
    $sets = [];
    $links = [];

    /** @todo move these paths outside from here */
    if (file_exists('app/data/bricks.objects.set')) {
        $handle = file('app/data/bricks.objects.set');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $sets[] = $item->jsonSerialize();
            /** @todo introduce LinkValue Object */
            $links[] = [
                'rel' => 'set ' . $item->get('code'),
                'href' => '/set/' . $item->get('code')
            ];
        }
    }


    $json = $app['response']->getCollection('/sets/', $sets, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/insights/', function () use ($app) {
    $insights = [];
    $links = [];

    /** @todo move outsite path data responsibility */
    if (file_exists('app/data/bricks.objects.insight')) {
        $handle = file('app/data/bricks.objects.insight');
        foreach ($handle as $set) {
            $insight = unserialize($set);
            $insights[] = $insight->jsonSerialize();
            /** @todo introduce LinkValue Object */
            $links[] = ['rel' => 'set ' . $insight->get('set'), 'href' => '/set/' . $insight->get('set')];
            $links[] = ['rel' => 'set ' . $insight->get('shop'), 'href' => '/shop/' . $insight->get('shop')];
            $links[] = ['rel' => 'insight ' . $insight->getTimestamp(), 'href' => '/insight/' . $insight->getTimestamp()];
        }
    }

    $json = $app['response']->getCollection('/insights/', $insights, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/shops/', function () use ($app) {
    $shops = [];
    $links = [];

    if (file_exists('app/data/bricks.objects.shop')) {
        $handle = file('app/data/bricks.objects.shop');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $shops[] = $item->jsonSerialize();
            $links[] = ['rel' => 'shop ' . $item->getSlug(), 'href' => '/shop/' . $item->getSlug()];
        }
    }

    $json = $app['response']->getCollection('/shops/', $shops, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->post('/api/v1/set/', function (Request $request) use ($app) {
    if (file_exists('app/data/bricks.objects.set')) {
        $handle = file('app/data/bricks.objects.set');
        foreach ($handle as $set) {
            $item = unserialize($set);
            if ($item->get('code') == $request->request->get('code')) {
                return new JsonResponse([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Set ' . $item->get('code') . ' already exists',
                ], 409);
            }
        }
    }

    $setObject = Set::box([
        'code' => $request->request->get('code'),
        'name' => $request->request->get('name'),
        'pieces' => $request->request->get('pieces'),
        'update' => new \DateTime('now'),
    ]);

    (new Persist(
        $setObject,
        new Bricks\Services\NamesGenerator(),
        $app['logger']
    ))->persist();

    $setAsArray = $setObject->jsonSerialize();

    return new JsonResponse($setAsArray, 201);
});

$app->post('/api/v1/shop/', function (Request $request) use ($app) {
    if (file_exists('app/data/bricks.objects.shop')) {
        $handle = file('app/data/bricks.objects.shop');
        foreach ($handle as $set) {
            $item = unserialize($set);
            if ($item->get('address') == $request->request->get('address')) {
                return new JsonResponse([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Set ' . $item->get('code') . ' already exists',
                ], 409);
            }
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

return $app;
