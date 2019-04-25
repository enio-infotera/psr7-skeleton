<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action Interface.
 */
interface ActionInterface
{
    /**
     * Action.
     *
     * @param ServerRequestInterface $request the request
     *
     * @return ResponseInterface the response
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface;
}
