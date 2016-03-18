<?php

use Bricks\Factories\ResponseFactory;
use Bricks\Insight;
use Bricks\Persist;
use Bricks\Response\ErrorResponse;
use Bricks\Set;
use Bricks\Shop;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once 'vendor/autoload.php';

$app = new Silex\Application();
$app['response'] = function () {
    return new ResponseFactory();
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
    $insightCollection = file('app/data/bricks.insight');
    foreach ($insightCollection as $insightItem) {
        $insight = unserialize($insightItem);
        if ($insight->getTimestamp() == $timestamp) {
            $json = $app['response']->getInsight($insight);
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
    $handle = file('app/data/bricks.shop');
    foreach ($handle as $shop) {
        $item = unserialize($shop);
        if ($item->getSlug() == $slug) {
            $json = $app['response']->getShop($item);
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

$app->get('/api/v1/set/{code}', function ($code) use ($app) {
    $handle = file('app/data/bricks.set');
    foreach ($handle as $set) {
        $item = unserialize($set);
        if ($item->getCode() == $code) {
            $json = $app['response']->getSet($item);
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
    if (file_exists('app/data/bricks.set')) {
        $handle = file('app/data/bricks.set');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $sets[] = $item->jsonSerialize();
            /** @todo introduce LinkValue Object */
            $links[] = [
                'rel' => 'set ' . $item->getCode(),
                'href' => '/set/' . $item->getCode()
            ];
        }
    }

    $json = $app['response']->getLegoSets($sets, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/insights/', function () use ($app) {
    $insights = [];
    $links = [];

    /** @todo move outsite path data responsibility */
    if (file_exists('app/data/bricks.insight')) {
        $handle = file('app/data/bricks.insight');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $insights[] = $item->jsonSerialize();
            /** @todo introduce LinkValue Object */
            $links[] = ['rel' => 'set ' . $item->getSet(), 'href' => '/set/' . $item->getSet()];
            $links[] = ['rel' => 'set ' . $item->getShop(), 'href' => '/shop/' . $item->getShop()];
            $links[] = ['rel' => 'insight ' . $item->getTimestamp(), 'href' => '/insight/' . $item->getTimestamp()];
        }
    }

    $json = $app['response']->getInsightCollection($insights, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->get('/api/v1/shops/', function () use ($app) {
    $shops = [];
    $links = [];

    if (file_exists('app/data/bricks.shop')) {
        $handle = file('app/data/bricks.shop');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $shops[] = $item->jsonSerialize();
            $links[] = ['rel' => 'shop ' . $item->getSlug(), 'href' => '/shop/' . $item->getSlug()];
        }
    }

    $json = $app['response']->getShops($shops, $links);
    return new JsonResponse($json->asArray(), 200);
});

$app->post('/api/v1/set/{code}', function ($code) use ($app) {
    if (file_exists('app/data/bricks.set')) {
        $handle = file('app/data/bricks.set');
        foreach ($handle as $set) {
            $item = unserialize($set);
            if ($item->getCode() == $code) {
                return new JsonResponse([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Set ' . $item->getCode() . ' already exists',
                ], 409);
            }
        }
    }

    $set = Set::fromCode($code);
    Persist::jsonSerializable($set);
    $setAsArray = $set->jsonSerialize();

    return new JsonResponse($setAsArray, 201);
});

$app->post('/api/v1/shop/', function (Request $request) use ($app) {
    if (file_exists('app/data/bricks.shop')) {
        $handle = file('app/data/bricks.shop');
        foreach ($handle as $set) {
            $item = unserialize($set);
            if ($item->getAddress() == $request->request->get('address')) {
                return new JsonResponse([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Set ' . $item->getCode() . ' already exists',
                ], 409);
            }
        }
    }

    $shop = Shop::fromRequest($request);
    Persist::jsonSerializable($shop);
    $shopAsArray = $shop->jsonSerialize();

    return new JsonResponse($shopAsArray, 201);
});

$app->post('/api/v1/insight/', function (Request $request) use ($app) {
    $insight = Insight::fromRequest($request);
    Persist::jsonSerializable($insight);
    $insihtAsArray = $insight->jsonSerialize();

    return new JsonResponse($insihtAsArray, 201);
});

$app->error(function (\Exception $e) {
    return new JsonResponse(
        ErrorResponse::withDefaultMessage()
            ->jsonSerialize(),
        404
    );
});

return $app;
