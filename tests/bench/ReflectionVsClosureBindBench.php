<?php

namespace Tests\Bench;

use Lewis\ReflectionVsClosureGetObjectVars\AbstractBaseJobClosure;
use Lewis\ReflectionVsClosureGetObjectVars\AbstractBaseJobReflection;
use Lewis\ReflectionVsClosureGetObjectVars\Contracts\InternalDataAccessInterface;

class ReflectionVsClosureBindBench
{
    /**
     * @Warmup(100000)
     * @OutputTimeUnit("microseconds")
     * @OutputMode("throughput")
     * @Iterations(100)
     * @Revs(100000)
     */
    public function benchReflectionImplementation()
    {
        $job = new Class extends AbstractBaseJobReflection {
            private $internal;

            public function __construct($secret = 'shhh, I\'m secret')
            {
                $this->internal = $secret;
            }
        };
        ob_start();
        var_dump($job->getInternalState());
        ob_end_clean();
    }

    /**
     * @Warmup(100000)
     * @OutputTimeUnit("microseconds")
     * @OutputMode("throughput")
     * @Iterations(100)
     * @Revs(100000)
     */
    public function benchClosureImplementation()
    {
        $job = new Class extends AbstractBaseJobClosure {
            private $internal;

            public function __construct($secret = 'shhh, I\'m secret')
            {
                $this->internal = $secret;
            }
        };
        ob_start();
        var_dump($job->getInternalState());
        ob_end_clean();
    }

    /**
     * @Warmup(100000)
     * @OutputTimeUnit("microseconds")
     * @OutputMode("throughput")
     * @Iterations(100)
     * @Revs(100000)
     */
    public function benchBetterDesignedClassWithoutMagic()
    {
        $job = new Class implements InternalDataAccessInterface {
            private $internal;

            public function __construct($secret = 'shhh, I\'m secret')
            {
                $this->internal = $secret;
            }

            public function getInternalState(): array
            {
                return [
                    'internal' => $this->internal,
                ];
            }
        };
        ob_start();
        var_dump($job->getInternalState());
        ob_end_clean();
    }
}