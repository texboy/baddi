<?php
declare(strict_types=1);

namespace Texboy\BadDi;

use ArrayAccess;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use Texboy\BadDi\Exceptions\NotFoundException;

class Container implements ContainerInterface, ArrayAccess
{
    private $services;

    /**
     * @param array $services
     */
    public function __construct(array $services = [])
    {
        $this->services = $services;
    }

    /**
     * @param array $services
     */
    public function setServices(array $services): void
    {
        $this->services = $services;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $item = $this->resolve($id);
        if (!($item instanceof ReflectionClass)) {
            return $item;
        }
        return $this->getInstance($item);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        try {
            $item = $this->resolve($id);
        } catch (NotFoundException $e) {
            return false;
        }
        return $item->isInstantiable();

    }

    /**
     * @param $id
     * @return ReflectionClass
     * @throws NotFoundException
     */
    private function resolve($id)
    {
        try {
            $name = $id;
            if (isset($this->services[$id])) {
                $name = $this->services[$id];
                if (is_callable($name)) {
                    return $name();
                }
            }
            return (new ReflectionClass($name));
        } catch (ReflectionException $exception) {
            throw new NotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function getInstance(ReflectionClass $item)
    {
        $constructor = $item->getConstructor();
        if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0) {
            return $item->newInstance();
        }
        $params = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type !== null) {
                $params[] = $this->get($type->getName());
            }
        }
        return $item->newInstanceArgs($params);
    }

    public function set(string $key, $value)
    {
        $this->services[$key] = $value;
        return $this;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->services[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|void
     * @throws NotFoundException
     */
    public function offsetGet($offset)
    {
        if (!isset($this->services[$offset])) {
            throw new NotFoundException(sprintf("Definition for the %s does not exist in the services array", [$offset]));
        }
        return $this->get($this->services[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->services[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->services[$offset])) {
            unset($this->services[$offset]);
        }
    }
}
