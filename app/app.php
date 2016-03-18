<?php

use Bricks\Insight;
use Bricks\Persist;
use Bricks\Set;
use Bricks\Shop;
use Bricks\Response\ErrorResponse;
use Bricks\Response\Response as BricksResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once 'vendor/autoload.php';

$app = new Silex\Application();

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
    $response = BricksResponse::createEmpty();
    $response = $response->withKeyValue('sets', count(file('data/bricks.set')));
    $response = $response->withKeyValue('insights', count(file('data/bricks.insight')));
    $response = $response->withKeyValue('shops', count(file('data/bricks.shop')));
    $response = $response->withLink(['rel' => 'homepage', 'href' => 'http://localhost:8080/api/v1/homepage/']);
    $response = $response->withLink(['rel' => 'self', 'href' => 'http://localhost:8080/api/v1/stats/']);

    $content = json_encode(
        $response->asArray()
    );

    return new Response($content, 200);
});

$app->get('/api/v1/insight/{timestamp}', function ($timestamp) use ($app) {
    $handle = file('data/bricks.insight');
    foreach ($handle as $insight) {
        $item = unserialize($insight);
        if ($item->getTimestamp() == $timestamp) {
            $json = $item->jsonSerialize();
            $json['links'][] = [
                'rel' => 'homepage',
                'href' => 'http://localhost:8080/api/v1/homepage/',
            ];

            return new Response(
                json_encode($json),
                200
            );
        }
    }

    /** @todo define an error table with api error code */
    $status = 'error';
    $code = '32432423';
    $message = 'No insight found';

    return new Response(json_encode([
        'status' => $status,
        'code' => $code,
        'message' => $message,
    ]), 404);
});

$app->get('/api/v1/shop/{slug}', function ($slug) use ($app) {
    $handle = file('data/bricks.shop');
    foreach ($handle as $shop) {
        $item = unserialize($shop);
        if ($item->getSlug() == $slug) {
            $json = $item->jsonSerialize();
            $json['links'][] = [
                'rel' => 'homepage',
                'href' => 'http://localhost:8080/api/v1/homepage/',
            ];

            return new Response(
                json_encode($json),
                200
            );
        }
    }

    /** @todo define an error table with api error code */
    $status = 'error';
    $code = '2344324';
    $message = 'Shop not found';

    return new Response(json_encode([
        'status' => $status,
        'code' => $code,
        'message' => $message,
    ]), 404);
});

$app->get('/api/v1/set/{code}', function ($code) use ($app) {
    $handle = file('data/bricks.set');
    foreach ($handle as $set) {
        $item = unserialize($set);
        if ($item->getCode() == $code) {
            $json = $item->jsonSerialize();
            $json['links'][] = [
                'rel' => 'homepage',
                'href' => 'http://localhost:8080/api/v1/homepage/',
            ];

            return new Response(
                json_encode($json),
                200
            );
        }
    }
});

$app->get('/api/v1/homepage/', function () use ($app) {
    return json_encode([
        'links' => [[
            'rel' => 'self',
            'href' => 'http://localhost:8080/api/v1/homepage/',
        ],[
            'rel' => 'sets',
            'href' => 'http://localhost:8080/api/v1/sets/',
        ],[
            'rel' => 'shops',
            'href' => 'http://localhost:8080/api/v1/shops/',
        ],[
            'rel' => 'insights',
            'href' => 'http://localhost:8080/api/v1/insights/',
        ],[
            'rel' => 'stats',
            'href' => 'http://localhost:8080/api/v1/stats/',
        ],]
    ]);
});

$app->get('/api/v1/sets/', function () use ($app) {
    $sets = [];

    if (file_exists('data/bricks.set')) {
        $handle = file('data/bricks.set');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $sets[] = $item->jsonSerialize();
        }
    }

    $json = [
        'content' => $sets,
        'links' => [[
            'rel' => 'collection',
            'href' => 'http://localhost:8080/api/v1/sets/',
         ],],
    ];

    $json['links'][] = [
        'rel' => 'homepage',
        'href' => 'http://localhost:8080/api/v1/homepage/',
    ];

    return new Response(
        json_encode($json),
        200
    );
});

$app->get('/api/v1/insights/', function () use ($app) {
    $insights = [];

    if (file_exists('data/bricks.insight')) {
        $handle = file('data/bricks.insight');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $insights[] = $item->jsonSerialize();
        }
    }

    $json = [
        'content' => $insights,
        'links' => [[
            'rel' => 'collection',
            'href' => 'http://localhost:8080/api/v1/insights/',
         ],],
    ];

    $json['links'][] = [
        'rel' => 'homepage',
        'href' => 'http://localhost:8080/api/v1/homepage/',
    ];

    return new Response(
        json_encode($json),
        200
    );
});

$app->get('/api/v1/shops/', function () use ($app) {
    $shops = [];

    if (file_exists('data/bricks.shop')) {
        $handle = file('data/bricks.shop');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $shops[] = $item->jsonSerialize();
        }
    }

    $json = [
        'content' => $shops,
        'links' => [[
            'rel' => 'collection',
            'href' => 'http://localhost:8080/api/v1/shops/',
         ],],
    ];

    $json['links'][] = [
        'rel' => 'homepage',
        'href' => 'http://localhost:8080/api/v1/homepage/',
    ];

    return new Response(
        json_encode($json),
        200
    );
});

$app->post('/api/v1/set/{code}', function ($code) use ($app) {
    if (file_exists('data/bricks.set')) {
        $handle = file('data/bricks.set');
        foreach ($handle as $set) {
            $item = unserialize($set);
            if ($item->getCode() == $code) {
                return new Response(json_encode([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Set ' . $item->getCode() . ' already exists',
                ]), 409);
            }
        }
    }

    $set = Set::fromCode($code);
    Persist::jsonSerializable($set);
    $json = $set->jsonSerialize();
    $content = json_encode($json);

    return new Response(
        $content,
        201
    );
});

$app->post('/api/v1/shop/', function (Request $request) use ($app) {
    if (file_exists('data/bricks.shop')) {
        $handle = file('data/bricks.shop');
        foreach ($handle as $set) {
            $item = unserialize($set);
            if ($item->getAddress() == $request->request->get('address')) {
                return new Response(json_encode([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Set ' . $item->getCode() . ' already exists',
                ]), 409);
            }
        }
    }

    $shop = Shop::fromRequest($request);
    Persist::jsonSerializable($shop);
    $json = $shop->jsonSerialize();

    $content = json_encode($json);

    return new Response(
        $content,
        201
    );
});

$app->post('/api/v1/insight/', function (Request $request) use ($app) {
    $insight = Insight::fromRequest($request);
    Persist::jsonSerializable($insight);
    $json = $insight->jsonSerialize();

    $content = json_encode($json);

    return new Response(
        $content,
        201
    );
});

$app->error(function (\Exception $e) {
    return new Response(
        ErrorResponse::withDefaultMessage()
            ->jsonSerialize(),
        404
    );
});

return $app;
