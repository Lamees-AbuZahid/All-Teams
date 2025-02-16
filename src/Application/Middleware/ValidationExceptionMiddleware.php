<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\ResponseFormatter;
use App\Services\RequestService;
use App\Contracts\SessionInterface;
use App\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

readonly class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface         $session,
        private RequestService           $requestService,
        private ResponseFormatter        $responseFormatter
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $response = $this->responseFactory->createResponse();

            if ($this->requestService->isXhr($request)) {
                return $this->responseFormatter->asJson($response->withStatus(422), $e->errors);
            }

            $referer  = $this->requestService->getReferer($request);
            $oldData  = $request->getParsedBody();

            $sensitiveFields = ['password', 'confirm_new_Password'];

            $this->session->flash('errors', $e->errors);
            $this->session->flash('old', array_diff_key($oldData, array_flip($sensitiveFields)));

            return $response->withHeader('Location', $referer)->withStatus(302);
        }
    }
}
