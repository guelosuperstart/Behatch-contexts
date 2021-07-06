<?php

declare(strict_types=1);

namespace Behatch\HttpCall;

use ReflectionNamedType;
use Behat\Behat\Context\Argument\ArgumentResolver;

class HttpCallResultPoolResolver implements ArgumentResolver
{
    private $dependencies;

    public function __construct(/* ... */)
    {
        $this->dependencies = [];

        foreach (\func_get_args() as $param) {
            $this->dependencies[\get_class($param)] = $param;
        }
    }

    public function resolveArguments(\ReflectionClass $classReflection, array $arguments)
    {
        $constructor = $classReflection->getConstructor();
        if (null !== $constructor) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $className = null;

                if ($parameter->getType() instanceof ReflectionNamedType && !$parameter->getType()->isBuiltin()) {
                    $className = $parameter->getType()->getName();
                }

                if (null !== $className && isset($this->dependencies[$className])) {
                    $arguments[$parameter->name] = $this->dependencies[$className];
                }
            }
        }

        return $arguments;
    }
}
