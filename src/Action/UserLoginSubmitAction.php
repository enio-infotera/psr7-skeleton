<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Domain\User\Locale;
use App\Http\RouterUrl;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
class UserLoginSubmitAction implements ActionInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Locale
     */
    protected $locale;

    /**
     * @var RouterUrl
     */
    protected $routerUrl;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param RouterUrl $routerUrl
     * @param Auth $auth
     * @param Locale $locale
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RouterUrl $routerUrl,
        Auth $auth,
        Locale $locale
    ) {
        $this->responseFactory = $responseFactory;
        $this->routerUrl = $routerUrl;
        $this->auth = $auth;
        $this->locale = $locale;
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
        $data = (array)$request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        $user = $this->auth->authenticate($username, $password);

        if (!empty($user) && $user->getLocale() !== null) {
            $this->locale->setLanguage($user->getLocale());
            $url = $this->routerUrl->pathFor('root');
        } else {
            $url = $this->routerUrl->pathFor('login');
        }

        return $this->responseFactory->createResponse()
            ->withStatus(StatusCode::STATUS_FOUND)
            ->withHeader('Location', $url);
    }
}
