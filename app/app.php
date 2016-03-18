<?php

use Bricks\Insight;
use Bricks\Persist;
use Bricks\Response\ErrorResponse;
use Bricks\Response\Response as BricksResponse;
use Bricks\Set;
use Bricks\Shop;
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
    $response = BricksResponse::createEmpty()
        ->withKeyValue('sets', count(file('data/bricks.set')))
        ->withKeyValue('insights', count(file('data/bricks.insight')))
        ->withKeyValue('shops', count(file('data/bricks.shop')))
        ->withLink(['rel' => 'homepage', 'href' => 'http://localhost:8080/api/v1/homepage/'])
        ->withLink(['rel' => 'self', 'href' => 'http://localhost:8080/api/v1/stats/']);

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
            $response = BricksResponse::createEmpty()
                ->withKeyValue('code', $item->getCode())
                ->withKeyValue('update', $item->getUpdate())
                ->withLink(['rel' => 'self', 'href' => 'http://localhost:8080/api/v1/set/' . $item->getCode()])
                ->withLink(['rel' => 'homepage', 'href' => 'http://localhost:8080/api/v1/homepage/'])
                ->withLink(['rel' => 'collection', 'href' => 'http://localhost:8080/api/v1/sets/']);

            return new Response(
                json_encode($response->asArray()),
                200
            );
        }
    }
});

$app->get('/api/v1/homepage/', function () use ($app) {
    $response = BricksResponse::createEmpty()
        ->withLink(['rel' => 'homepage', 'href' => 'http://localhost:8080/api/v1/homepage/'])
        ->withLink(['rel' => 'sets', 'href' => 'http://localhost:8080/api/v1/sets/'])
        ->withLink(['rel' => 'shops', 'href' => 'http://localhost:8080/api/v1/shops/'])
        ->withLink(['rel' => 'insights', 'href' => 'http://localhost:8080/api/v1/insights/'])
        ->withLink(['rel' => 'stats', 'href' => 'http://localhost:8080/api/v1/stats/'])
    ;

    return json_encode($response->asArray());
});

$app->get('/api/v1/sets/', function () use ($app) {
    $sets = [];
    $links = [];

    if (file_exists('data/bricks.set')) {
        $handle = file('data/bricks.set');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $sets[] = $item->jsonSerialize();
            $links[] = [
                'rel' => 'set ' . $item->getCode(),
                'href' => 'http://localhost:8080/api/v1/set/' . $item->getCode()
            ];
        }
    }

    $response = BricksResponse::createEmpty()
        ->withKeyValue('collection', $sets)
        ->withKeyValue('links', $links)
        ->withLink(['rel' => 'homepage', 'href' => 'http://localhost:8080/api/v1/homepage/'])
        ->withLink(['rel' => 'collection', 'href' => 'http://localhost:8080/api/v1/sets/'])
    ;

    return new Response(
        json_encode($response->asArray()),
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
            $links[] = [
                'rel' => 'set ' . $item->getSet(),
                'href' => 'http://localhost:8080/api/v1/set/' . $item->getSet()
            ];
            $links[] = [
                'rel' => 'set ' . $item->getShop(),
                'href' => 'http://localhost:8080/api/v1/shop/' . $item->getShop()
            ];
            $links[] = [
                'rel' => 'insight ' . $item->getTimestamp(),
                'href' => 'http://localhost:8080/api/v1/insight/' . $item->getTimestamp()
            ];
        }
    }

    $response = BricksResponse::createEmpty()
        ->withKeyValue('collection', $insights)
        ->withKeyValue('links', $links)
        ->withLink(['rel' => 'homepage', 'href' => 'http://localhost:8080/api/v1/homepage/'])
        ->withLink(['rel' => 'collection', 'href' => 'http://localhost:8080/api/v1/insights/'])
    ;

    return new Response(
        json_encode($response->asArray()),
        200
    );
});

$app->get('/api/v1/shops/', function () use ($app) {
    $shops = [];
    $links = [];

    if (file_exists('data/bricks.shop')) {
        $handle = file('data/bricks.shop');
        foreach ($handle as $set) {
            $item = unserialize($set);
            $shops[] = $item->jsonSerialize();
            $links[] = [
                'rel' => 'shop ' . $item->getSlug(),
                'href' => 'http://localhost:8080/api/v1/shop/' . $item->getSlug()
            ];
        }
    }

    $response = BricksResponse::createEmpty()
        ->withKeyValue('collection', $shops)
        ->withKeyValue('links', $links)
        ->withLink(['rel' => 'homepage', 'href' => 'http://localhost:8080/api/v1/homepage/'])
        ->withLink(['rel' => 'collection', 'href' => 'http://localhost:8080/api/v1/shops/'])
    ;

    return new Response(
        json_encode($response->asArray()),
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
