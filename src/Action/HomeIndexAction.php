<?php

namespace App\Action;

use App\Http\HtmlResponder;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class HomeIndexAction implements ActionInterface
{
    /**
     * @var HtmlResponder
     */
    private $responder;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * Constructor.
     *
     * @param HtmlResponder $responder the responder
     * @param SessionInterface $session the session handler
     */
    public function __construct(HtmlResponder $responder, SessionInterface $session)
    {
        $this->responder = $responder;
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
        return $this->responder->render('Home/home-index.twig', $viewData);
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
