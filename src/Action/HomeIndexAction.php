<?php

namespace App\Action;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as Twig;

/**
 * Action.
 */
final class HomeIndexAction implements ActionInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory the response factory
     * @param Twig $twig twig
     * @param SessionInterface $session the session handler
     */
    public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig, SessionInterface $session)
    {
        $this->responseFactory = $responseFactory;
        $this->twig = $twig;
        $this->session = $session;
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
        // Increment counter
        $counter = $this->session->get('counter') ?? 0;
        $this->session->set('counter', $counter++);

        $viewData = [
            'text' => $this->getText(),
            'counter' => $counter,
            'url' => $request->getUri(),
        ];

        // Render template
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($this->twig->render('Home/home-index.twig', $viewData));

        return $response;
    }

    /**
     * Translate text.
     *
     * @return string[] Array with translated text
     */
    private function getText(): array
    {
        return [
            'Loaded successfully!' => __('Loaded successfully!'),
            'Loading...' => __('Loading...'),
            'Hello World' => __('Hello World'),
            'Current user' => __('Current user'),
            'User-ID' => __('User-ID'),
            'Username' => __('Username'),
            'User ID' => __('User ID'),
            'Current time' => __('Current time'),
            'Message' => __('Message'),
            'Selected' => __('Selected'),
        ];
    }
}
