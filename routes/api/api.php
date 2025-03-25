<?php

/** @var Illuminate\Routing\Router $router */

$router->get('', function () {
    return response(null, 204);
});
