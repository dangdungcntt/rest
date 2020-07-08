<?php

namespace Core\Support;

class Response extends \React\Http\Response
{
    public function json(array $data, int $status = 200): Response
    {
        $headers                 = $this->getHeaders();
        $headers['Content-Type'] = 'application/json';

        return new Response($status, $headers, json_encode($data));
    }

    public function redirect(string $to, int $status = 302, array $headers = []): Response
    {
        $headers             = array_merge($this->getHeaders(), $headers);
        $headers['Location'] = $to;

        return new Response($status, $headers);
    }
}