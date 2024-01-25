<?php

declare(strict_types=1);
use App\Application\Actions\Majd\Majd;
use Slim\App;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;



use App\Application\Actions\AsemYamak\TestAction;
use App\Application\Actions\Majd\test;



return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });



    $app->get('/asemyamak/list', TestAction::class);
    $app->get('/Majd/list', Majd::class);


    // $app->group('/asemyamak', function (Group $group) {
    // });

};


