<?php

namespace App\Action;

use App\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class HomePingAction implements ActionInterface
{
    /** @var JsonResponder */
    private $responder;

    /**
     * Constructor.
     *
     * @param JsonResponder $responder The responder
     */
    public function __construct(JsonResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request the request
     *
     * @return ResponseInterface the response
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->encode($request->getParsedBody());
    }
}
