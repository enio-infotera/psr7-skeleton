<?php

namespace App\Action;

use App\Http\HtmlResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserLoginIndexAction implements ActionInterface
{
    /** @var HtmlResponder */
    private $responder;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder the resonder
     */
    public function __construct(HtmlResponder $responder)
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
        return $this->responder->render('User/user-login.twig');
    }
}
