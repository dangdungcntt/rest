<?php


namespace Core\Exceptions;


use Core\Support\DumpDieException;
use React\Http\Response;

class Handler
{
    /**
     * @param  \Throwable  $throwable
     * @return Response
     * @throws \Throwable
     */
    public function handle(\Throwable $throwable): Response
    {
        $exceptionMessage = '';
        if ($throwable instanceof DumpDieException) {
            return new Response(200, ['Content-Type' => 'text/plain'],
                $throwable->getMessage());
        }

        try {
            return $this->unknownException($throwable);
        } catch (\Throwable $throwable1) {
            if (app()->isDebug()) {
                $exceptionMessage = $throwable->getMessage().PHP_EOL.$throwable->getTraceAsString();
                return new Response(500, ['Content-Type' => 'text/plain'],
                    $exceptionMessage);
            }

            throw $throwable;
        } finally {
            if ($exceptionMessage) {
                logger($exceptionMessage);
            }
        }
    }

    protected function unknownException(\Throwable $throwable): Response
    {
        throw $throwable;
    }

}