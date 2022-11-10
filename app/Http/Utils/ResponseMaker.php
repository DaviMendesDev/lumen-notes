<?php

namespace App\Http\Utils;

use Carbon\Carbon;

class ResponseMaker
{
    protected \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory $response;

    public function success(string $message, array $data = []): \Illuminate\Http\JsonResponse
    {
        return $this->json($message, 200, $data);
    }

    public function addHeaders(array $headers): static
    {
        foreach($headers as $headerName => $headerValue)
            $this->response->header($headerName, $headerValue);

        return $this;
    }

    public function getResponse(): \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
    {
        return $this->response;
    }

    public function json(string $message, int $status, array $data = []): \Illuminate\Http\JsonResponse
    {
        $currentTime = Carbon::now();

        return $this->response->json(
            [
                'message' => $message,
                'timestamp' => $currentTime->timestamp,
                'timestampReadable' => $currentTime->toDateString(),
                'status' => $status,
                'data' => $data,
            ],
            $status,
            $this->response->headers
        );
    }
}
