<?php


namespace Core\Contracts;


interface DIContainer
{
    public function resolve(string $name, ?string $parentName = null);

    public function bind(string $name, $value, ?string $parentName = null);
}