<?php

namespace Lewis\ReflectionVsClosureGetObjectVars;

use Lewis\ReflectionVsClosureGetObjectVars\Contracts\InternalDataAccessInterface;

abstract class AbstractBaseJobClosure implements InternalDataAccessInterface
{
    public function getInternalState(): array
    {
        return \Closure::bind(fn() => get_object_vars($this), $this, static::class)();
    }
}