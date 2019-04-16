<?php

namespace App\Action;

use App\Http\HtmlResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as Twig;

/**
 * Action.
 */
class UserLoginIndexAction implements ActionInterface
{
    /**
     * @var HtmlResponder
     */
    private $responder;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder
     * @param Twig $twig
     */
    public function __construct(HtmlResponder $responder, Twig $twig)
    {
        $this->responder = $responder;
        $this->twig = $twig;
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
