<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Domain\User\Locale;
use App\Factory\LoggerFactory;
use App\Http\RouterUrl;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Action.
 */
final class UserLoginSubmitAction implements ActionInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var Auth */
    private $auth;

    /** @var Locale */
    private $locale;

    /** @var RouterUrl */
    private $routerUrl;

    /** @var LoggerInterface */
    private $log;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory the response factory
     * @param RouterUrl $routerUrl router url
     * @param Auth $auth auth
     * @param Locale $locale locale
     * @param LoggerFactory $loggerFactory logger factory
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RouterUrl $routerUrl,
        Auth $auth,
        Locale $locale,
        LoggerFactory $loggerFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->routerUrl = $routerUrl;
        $this->auth = $auth;
        $this->locale = $locale;
        $this->log = $loggerFactory->createLogger('user_login_submit');
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
            $this->log->warning(sprintf('Login failed for user: %s', $username));
            $url = $this->routerUrl->pathFor('login');
        }

        return $this->responseFactory->createResponse()
            ->withStatus(StatusCode::STATUS_FOUND)
            ->withHeader('Location', $url);
    }
}
