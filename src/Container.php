<?php
declare(strict_types=1);

namespace Texboy\BadDi;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use Texboy\BadDi\Exceptions\NotFoundException;

class Container implements ContainerInterface
{
    private $services = [];

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
}
