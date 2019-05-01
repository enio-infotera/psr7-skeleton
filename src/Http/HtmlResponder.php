<?php

namespace App\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as Twig;

/**
 * A generic HTML Responder.
 */
final class HtmlResponder
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var Twig */
    private $twig;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory the response factory
     * @param Twig $twig twig
     */
    public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig)
    {
        $this->responseFactory = $responseFactory;
        $this->twig = $twig;
    }

    /**
     * Render template and return a html response.
     *
     * @param string $name The template file
     * @param array $viewData The view data
     *
     * @return ResponseInterface The response
     */
    public function render(string $name, array $viewData = []): ResponseInterface
    {
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html; charset=utf-8');

        $response->getBody()->write($this->twig->render($name, $viewData));

        return $response;
    }
}
