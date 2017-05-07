<?php

namespace App\Http;

use Exception;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

class ActionContext
{
    protected $request;

    protected $response;

    public function __construct(Request $request = null, Response $response = null)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getCookie()
    {
        // todo
        throw new Exception('Not implemented');
    }

    public function getSession()
    {
        // todo
        throw new Exception('Not implemented');
    }
}
