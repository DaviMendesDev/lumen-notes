<?php

namespace App\Http\Controllers;

use App\Http\Utils\ResponseMaker;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var \Laravel\Lumen\Application|mixed
     */
    protected mixed $responseMaker;

    public function __construct()
    {
        $this->responseMaker = app(ResponseMaker::class);
    }


}
