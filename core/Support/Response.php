<?php

namespace Core\Support;

use React\Http\Io\HttpBodyStream;
use React\Stream\ReadableStreamInterface;
use function RingCentral\Psr7\stream_for;

class Response extends \React\Http\Response
{
    public function stream(ReadableStreamInterface $stream): Response
    {
        return $this->withBody(new HttpBodyStream($stream, null));
    }

    public function json($data, int $status = 200): Response
    {
        return $this
            ->withHeader('Content-Type', 'application/json')
            ->withBody(stream_for(json_encode($data)))
            ->withStatus($status);
    }

    public function redirect(string $to, int $status = 302, array $headers = []): Response
    {
        return $this->withHeader('Location', $to)->withHeaders($headers);
    }

    public function withHeaders(array $headers)
    {
        $new = clone $this;
        $new->setHeaders($headers);
        return $new;
    }
}