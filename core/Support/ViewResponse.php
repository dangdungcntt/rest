<?php

namespace Core\Support;

class ViewResponse
{
    protected array $headers = [
        'Content-Type' => 'text/html'
    ];
    protected int $statusCode;
    protected string $viewName;
    protected array $data;

    public function __construct(string $viewName, array $data = [])
    {
        $this->viewName = $viewName;
        $this->data     = $data;
    }

    public function withHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function withHeader(string $key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withContentType($value)
    {
        return $this->withHeader('Content-Type', $value);
    }

    public function withStatus($status)
    {
        $this->statusCode = $status;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function render()
    {
        return app()->view->render($this->viewName, $this->data);
    }
}