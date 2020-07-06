<?php

namespace App\Support;

use React\Http\Response;

class ViewResponse
{
    protected array $headers = [];
    protected string $viewName;
    protected array $data;

    public function __construct(string $viewName, array $data = [])
    {
        $this->viewName = $viewName;
        $this->data     = $data;
    }

    public function withHeader(string $key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function render()
    {
        $html = app()->view->render($this->viewName, $this->data);
        return new Response(200, $this->headers, $html);
    }
}