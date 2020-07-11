<?php


namespace Core\DI;


use Core\Contracts\DIContainer;
use Core\Contracts\Singleton;
use Core\Exceptions\DICannotConstructException;

class Container implements DIContainer
{
    protected static Container $instance;

    protected array $resolved = [];

    protected array $bind = [];

    public static function getInstance()
    {
        return isset(self::$instance) ? self::$instance : self::$instance = new static();
    }

    /**
     * @param string $name
     * @param string|null $parentName
     * @return Singleton|mixed
     * @throws DICannotConstructException
     * @throws \ReflectionException
     */
    public function resolve(string $name, ?string $parentName = null)
    {
        if (is_string($parentName) && array_key_exists("$name-$parentName", $this->resolved)) {
            return $this->resolved["$name-$parentName"];
        }

        if (array_key_exists($name, $this->resolved)) {
            return $this->resolved[$name];
        }

        if (is_string($parentName) && array_key_exists("$name-$parentName", $this->bind)) {
            return $this->resolve($this->bind["$name-$parentName"], $parentName);
        }

        if (array_key_exists($name, $this->bind)) {
            return $this->resolve($this->bind[$name], $parentName);
        }

        $object = $this->constructInstance($name);

        if ($object instanceof Singleton) {
            if (is_string($parentName) && isset($this->bind["$name-$parentName"])) {
                $this->resolved["$name-$parentName"] = $object;
            } else {
                $this->resolved[$name] = $object;
            }
        }

        return $object;
    }

    /**
     * @param $name
     * @return mixed
     * @throws DICannotConstructException
     * @throws \ReflectionException
     */
    protected function constructInstance($name)
    {
        if ($name instanceof \Closure) {
            return $name($this);
        }

        if (!is_string($name)) {
            return $name;
        }

        if (interface_exists($name)) {
            throw new \LogicException(sprintf('Cannot construct interface %s  without bind. Please bind this interface in Container', $name));
        }

        if (!class_exists($name)) {
            throw new \LogicException(sprintf('Class %s do not exists ', $name));
        }

        $reflection  = new \ReflectionClass($name);
        $constructor = $reflection->getConstructor();

        if (is_null($constructor) || $constructor->getNumberOfParameters() == 0) {
            return new $name();
        }

        $resolvedParams = $this->buildParams($name, $constructor->getParameters());

        return new $name(...$resolvedParams);
    }

    /**
     * @param $name
     * @param $params
     * @return array
     * @throws DICannotConstructException
     * @throws \ReflectionException
     */
    protected function buildParams($name, $params)
    {
        $resolvedParams = [];
        foreach ($params as $param) {
            $type = $param->getType();

            if (is_null($type) || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $resolvedParams[] = $param->getDefaultValue();
                    continue;
                }
                throw new DICannotConstructException("Cannot construct $name because \${$param->getName()} is not initialize");
            }

            try {
                $resolvedParams[] = $this->resolve($param->getClass()->getName(), $name);
            } catch (DICannotConstructException $exception) {
                if ($param->isDefaultValueAvailable()) {
                    $resolvedParams[] = $param->getDefaultValue();
                    continue;
                }
                throw $exception;
            }
        }
        return $resolvedParams;
    }

    public function bind(string $name, $value, ?string $parentName = null)
    {
        if (is_string($parentName)) {
            $name = "$name-$parentName";
        }
        $this->bind[$name] = $value;
        return $this;
    }
}