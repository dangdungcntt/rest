<?php

namespace Core\Support;

use React\Http\Io\HttpBodyStream;
use React\Stream\ReadableStreamInterface;

class Response extends \React\Http\Response
{
    public function withHeaders(array $headers)
    {
        $new = clone $this;
        $new->setHeaders($headers);
        return $new;
    }

    public function stream(ReadableStreamInterface $stream): Response
    {
        return $this->withBody(new HttpBodyStream($stream, null));
    }

    public function json($data, int $status = 200): Response
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