<?php

namespace Lewis\ReflectionVsClosureGetObjectVars;

use Lewis\ReflectionVsClosureGetObjectVars\Contracts\InternalDataAccessInterface;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractBaseJobReflection implements InternalDataAccessInterface
{
    public function getInternalState(): array
    {
        $reflectionClass = new ReflectionClass($this);
        return array_reduce(
            $reflectionClass->getProperties(),
            function(array $out, ReflectionProperty $property) {
                $wasAccessible = $property->isPrivate() || $property->isProtected();
                $property->setAccessible(true);
                $out[$property->getName()] = $property->isInitialized($this) ? $property->getValue($this) : null;
                $property->setAccessible(!$wasAccessible);
                return $out;
            },
            []
        );
    }
}