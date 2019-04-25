<?php

namespace App\Http;

use FastRoute\RouteParser;
use FastRoute\RouteParser\Std as StdParser;
use InvalidArgumentException;
use League\Route\Router;
use Psr\Http\Message\RequestInterface;

/**
 * Creating URLs for a named route.
 */
final class RouterUrl
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Router
     */
    private $router;

    /**
     * Parser.
     *
     * @var RouteParser
     */
    private $routeParser;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param RouteParser|null $parser
     */
    public function __construct(Router $router, RouteParser $parser = null)
    {
        $this->router = $router;
        $this->routeParser = $parser ?: new StdParser();
    }

    /**
     * Base path used in pathFor().
     *
     * @var string
     */
    private $basePath = '';

    /**
     * Set the base path used in pathFor().
     *
     * @param string $basePath
     *
     * @return void
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Get the base path used in pathFor().
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Build the path for a named route excluding the base path.
     *
     * @param string $name Route name
     * @param array $data Named argument replacement data
     * @param array $queryParams Optional query string parameters
     *
     * @throws InvalidArgumentException If required data not provided
     *
     * @return string
     */
    private function relativePathFor(string $name, array $data = [], array $queryParams = []): string
    {
        $pattern = $this->router->getNamedRoute($name)->getPath();
        $routeDatas = $this->routeParser->parse($pattern);

        // $routeDatas is an array of all possible routes that can be made. There is
        // one routedata for each optional parameter plus one for no optional parameters.
        //
        // The most specific is last, so we look for that first.
        $routeDatas = array_reverse($routeDatas);
        $segments = [];
        $segmentName = '';

        foreach ($routeDatas as $routeData) {
            foreach ($routeData as $item) {
                if (is_string($item)) {
                    // this segment is a static string
                    $segments[] = $item;
                    continue;
                }

                // This segment has a parameter: first element is the name
                if (!array_key_exists($item[0], $data)) {
                    // we don't have a data element for this segment: cancel
                    // testing this routeData item, so that we can try a less
                    // specific routeData item.
                    $segments = [];
                    $segmentName = $item[0];
                    break;
                }
                $segments[] = $data[$item[0]];
            }

            if (!empty($segments)) {
                // we found all the parameters for this route data, no need to check
                // less specific ones
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }

        $url = implode('', $segments);

        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * Build the path for a named route including the base path.
     *
     * @param string $name Route name
     * @param array $data Named argument replacement data
     * @param array $queryParams Optional query string parameters
     *
     * @throws InvalidArgumentException If required data not provided
     *
     * @return string
     */
    public function pathFor($name, array $data = [], array $queryParams = []): string
    {
        $url = $this->relativePathFor($name, $data, $queryParams);

        if ($this->basePath) {
            $url = $this->basePath . $url;
        }

        return $url;
    }
}
