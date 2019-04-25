<?php

namespace App\Middleware;

use App\Domain\User\Locale;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware.
 */
final class LanguageMiddleware implements MiddlewareInterface
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * Constructor.
     *
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get user language
        $locale = $this->locale->getLocale() ?? '';
        $domain = $this->locale->getDomain() ?? '';

        // Default language
        if (empty($locale)) {
            $locale = 'en_US';
            $domain = 'messages';
        }

        // Set language
        $this->locale->setLanguage($locale, $domain);

        return $handler->handle($request);
    }
}
