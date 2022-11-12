<?php

namespace App\Http\Controllers;

use App\Http\Utils\ResponseBuilder;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var ResponseBuilder
     */
    protected mixed $response;

    public function __construct(ResponseBuilder $responseBuilder)
    {
        $this->response = $responseBuilder;
    }
}
