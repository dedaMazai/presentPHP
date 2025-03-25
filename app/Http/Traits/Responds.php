<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use function app;

/**
 * trait Responds
 *
 * @package App\Http\Traits
 */
trait Responds
{
    protected function response(array $data = null): Response
    {
        /** @var Response $response */
        $response = app(ResponseFactory::class)->json($data);

        // Remove empty json object if no data given
        if ($data === null) {
            $response = $response->setContent(null);
        }

        return $response;
    }

    protected function empty(): Response
    {
        return $this->response()->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    protected function created(array $data = null): Response
    {
        return $this->response($data)->setStatusCode(Response::HTTP_CREATED);
    }

    protected function notAllowed(): Response
    {
        return $this->response()->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    protected function forbidden(): Response
    {
        return $this->response()->setStatusCode(Response::HTTP_FORBIDDEN);
    }
}
