<?php

namespace Lewis\ReflectionVsClosureGetObjectVars\Contracts;

interface InternalDataAccessInterface
{
    public function getInternalState(): array;
}