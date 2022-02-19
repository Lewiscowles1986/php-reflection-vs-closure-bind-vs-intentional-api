# PHP Bench Reflection vs Closure static::class binding vs Intentional Class design

## Background

This experiment is mostly to kick the tyres with phpbench, on a real problem I encountered at work.

> Reflection is slow right?

Okay, so how else without a massive refactor to access objects throughout an entire codebase?

This is just exploring three ways to refactor. Two short-term hacks and a third intentional approach.

## Running

> I'll assume you know how to `git clone` and `composer install`.

```
XDEBUG_MODE=off vendor/bin/phpbench run tests/bench --bootstrap=vendor/autoload.php --report=default --progress=blinken
```

> **NOTE:** `XDEBUG_MODE=off` prevents some weird errors I was seeing when running this.

### Results / Output

It really does not matter!

* Reflection
  * 25 times it was rounded to higher than 1 microsecond
  * max average for 100,000 iterations 2.765μs
* Closure Binding
  * 20 times it was rounded to higher than 1 microsecond
  * max average for 100,000 iterations 2.360μs
* Better design by making intentional classes
  * 0 times it was rounded to higher than 1 microsecond
  * max average for 100,000 iterations 1.390μs
  * operated below 1 microsecond average for multiple iterations
  * for some reason took 16 bytes more RAM

```
PHPBench (1.2.3) running benchmarks...
with PHP version 8.1.0, xdebug ✔, opcache ❌

\Tests\Bench\ReflectionVsClosureBindBench (#0 benchReflectionImplementation, #1 benchClosureImplementation, #2 benchBetterDesignedClassWithoutMagic)

#0  1     1     0     1     1     1     1     1     1     1
    1     1     0     0     0     1     1     1     1     1
    0     0     1     1     0     1     1     0     0     1
    1     1     1     1     1     0     1     1     1     1
    1     1     1     1     0     1     1     1     1     1
    1     0     1     0     1     0     1     1     1     0
    0     1     1     1     1     0     1     1     1     1
    1     0     0     1     1     1     0     1     0     1
    1     1     1     0     1     1     1     1     1     0
    1     1     0     1     0     1     0     1     1     0 (ops/μs) Mo0.574ops/μs (±16.15%)
#1  1     1     1     1     1     1     1     1     1     0
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     0     1     1     1     1
    0     1     0     1     0     1     1     0     1     1
    1     1     1     0     1     0     1     1     1     1
    1     1     1     1     1     0     0     1     1     1
    1     1     0     1     1     0     1     0     0     1
    1     0     0     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     0     1     1
    1     1     1     0     1     0     1     0     1     1 (ops/μs) Mo0.570ops/μs (±10.23%)
#2  1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1
    1     1     1     1     1     1     1     1     1     1 (ops/μs) Mo1.030ops/μs (±11.45%)

Subjects: 3, Assertions: 0, Failures: 0, Errors: 0
+------+------------------------------+--------------------------------------+-----+--------+----------+----------+--------------+----------------+
| iter | benchmark                    | subject                              | set | revs   | mem_peak | time_avg | comp_z_value | comp_deviation |
+------+------------------------------+--------------------------------------+-----+--------+----------+----------+--------------+----------------+
| 0    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.731μs  | -0.61σ       | -9.86%         |
| 1    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.708μs  | -0.68σ       | -11.04%        |
| 2    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.231μs  | +1.00σ       | +16.20%        |
| 3    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.691μs  | -0.74σ       | -11.94%        |
| 4    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.831μs  | -0.29σ       | -4.65%         |
| 5    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.716μs  | -0.66σ       | -10.60%        |
| 6    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.725μs  | -0.63σ       | -10.17%        |
| 7    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.735μs  | -0.60σ       | -9.65%         |
| 8    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.715μs  | -0.66σ       | -10.67%        |
| 9    | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.707μs  | -0.69σ       | -11.07%        |
| 10   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.915μs  | -0.02σ       | -0.28%         |
| 11   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.745μs  | -0.56σ       | -9.12%         |
| 12   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.728μs  | +2.61σ       | +42.07%        |
| 13   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.765μs  | +2.72σ       | +44.00%        |
| 14   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.508μs  | +1.90σ       | +30.62%        |
| 15   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.736μs  | -0.59σ       | -9.59%         |
| 16   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.694μs  | -0.73σ       | -11.78%        |
| 17   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.704μs  | -0.70σ       | -11.25%        |
| 18   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.742μs  | -0.57σ       | -9.25%         |
| 19   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.748μs  | -0.55σ       | -8.96%         |
| 20   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.090μs  | +0.55σ       | +8.85%         |
| 21   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.146μs  | +0.73σ       | +11.74%        |
| 22   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.711μs  | -0.68σ       | -10.90%        |
| 23   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.730μs  | -0.61σ       | -9.87%         |
| 24   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.378μs  | +1.48σ       | +23.87%        |
| 25   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.818μs  | -0.33σ       | -5.30%         |
| 26   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.727μs  | -0.62σ       | -10.05%        |
| 27   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.754μs  | +2.69σ       | +43.45%        |
| 28   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.438μs  | +1.67σ       | +26.98%        |
| 29   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.729μs  | -0.62σ       | -9.95%         |
| 30   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.760μs  | -0.52σ       | -8.34%         |
| 31   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.727μs  | -0.62σ       | -10.07%        |
| 32   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.747μs  | -0.56σ       | -8.99%         |
| 33   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.964μs  | +0.14σ       | +2.27%         |
| 34   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.752μs  | -0.54σ       | -8.76%         |
| 35   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.368μs  | +1.45σ       | +23.34%        |
| 36   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.946μs  | +0.08σ       | +1.37%         |
| 37   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.793μs  | -0.41σ       | -6.64%         |
| 38   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.802μs  | -0.38σ       | -6.15%         |
| 39   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.733μs  | -0.60σ       | -9.75%         |
| 40   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.858μs  | -0.20σ       | -3.23%         |
| 41   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.744μs  | -0.57σ       | -9.16%         |
| 42   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.966μs  | +0.15σ       | +2.37%         |
| 43   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.756μs  | -0.53σ       | -8.56%         |
| 44   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.226μs  | +0.99σ       | +15.96%        |
| 45   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.863μs  | -0.18σ       | -2.96%         |
| 46   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.749μs  | -0.55σ       | -8.91%         |
| 47   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.696μs  | -0.72σ       | -11.67%        |
| 48   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.721μs  | -0.64σ       | -10.35%        |
| 49   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.806μs  | -0.37σ       | -5.94%         |
| 50   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.729μs  | -0.62σ       | -9.95%         |
| 51   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.045μs  | +0.40σ       | +6.51%         |
| 52   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.746μs  | -0.56σ       | -9.05%         |
| 53   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.472μs  | +1.78σ       | +28.76%        |
| 54   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.719μs  | -0.65σ       | -10.47%        |
| 55   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.626μs  | +2.28σ       | +36.75%        |
| 56   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.731μs  | -0.61σ       | -9.84%         |
| 57   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.725μs  | -0.63σ       | -10.14%        |
| 58   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.738μs  | -0.59σ       | -9.49%         |
| 59   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.118μs  | +0.64σ       | +10.30%        |
| 60   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.189μs  | +0.87σ       | +14.02%        |
| 61   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.754μs  | -0.53σ       | -8.63%         |
| 62   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.755μs  | -0.53σ       | -8.59%         |
| 63   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.707μs  | -0.69σ       | -11.11%        |
| 64   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.722μs  | -0.64σ       | -10.30%        |
| 65   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.198μs  | +0.90σ       | +14.48%        |
| 66   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.791μs  | -0.42σ       | -6.70%         |
| 67   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.682μs  | -0.77σ       | -12.40%        |
| 68   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.675μs  | -0.79σ       | -12.78%        |
| 69   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.782μs  | -0.44σ       | -7.18%         |
| 70   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.689μs  | -0.74σ       | -12.02%        |
| 71   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.574μs  | +2.11σ       | +34.07%        |
| 72   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.590μs  | +2.16σ       | +34.91%        |
| 73   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.739μs  | -0.58σ       | -9.41%         |
| 74   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.694μs  | -0.73σ       | -11.79%        |
| 75   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.798μs  | -0.39σ       | -6.35%         |
| 76   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.501μs  | +1.87σ       | +30.27%        |
| 77   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.755μs  | -0.53σ       | -8.58%         |
| 78   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.024μs  | +0.33σ       | +5.41%         |
| 79   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.756μs  | -0.53σ       | -8.55%         |
| 80   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.763μs  | -0.51σ       | -8.18%         |
| 81   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.761μs  | -0.51σ       | -8.30%         |
| 82   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.709μs  | -0.68σ       | -10.99%        |
| 83   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.657μs  | +2.38σ       | +38.38%        |
| 84   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.731μs  | -0.61σ       | -9.83%         |
| 85   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.756μs  | -0.53σ       | -8.54%         |
| 86   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.790μs  | -0.42σ       | -6.75%         |
| 87   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.735μs  | -0.60σ       | -9.64%         |
| 88   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.682μs  | -0.77σ       | -12.41%        |
| 89   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.489μs  | +1.83σ       | +29.63%        |
| 90   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.728μs  | -0.62σ       | -10.00%        |
| 91   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.688μs  | -0.75σ       | -12.07%        |
| 92   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.090μs  | +0.55σ       | +8.87%         |
| 93   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.751μs  | -0.54σ       | -8.79%         |
| 94   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.173μs  | +0.82σ       | +13.19%        |
| 95   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.678μs  | -0.78σ       | -12.62%        |
| 96   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.027μs  | +0.35σ       | +5.59%         |
| 97   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.734μs  | -0.60σ       | -9.69%         |
| 98   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 1.704μs  | -0.70σ       | -11.24%        |
| 99   | ReflectionVsClosureBindBench | benchReflectionImplementation        |     | 100000 | 696,664b | 2.756μs  | +2.70σ       | +43.55%        |
| 0    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.777μs  | -0.49σ       | -5.04%         |
| 1    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.753μs  | -0.62σ       | -6.33%         |
| 2    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.717μs  | -0.81σ       | -8.23%         |
| 3    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.784μs  | -0.46σ       | -4.68%         |
| 4    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.914μs  | +0.22σ       | +2.28%         |
| 5    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.742μs  | -0.68σ       | -6.91%         |
| 6    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.820μs  | -0.27σ       | -2.76%         |
| 7    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.980μs  | +0.57σ       | +5.80%         |
| 8    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.699μs  | -0.90σ       | -9.22%         |
| 9    | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.050μs  | +0.93σ       | +9.56%         |
| 10   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.971μs  | +0.52σ       | +5.31%         |
| 11   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.768μs  | -0.54σ       | -5.53%         |
| 12   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.815μs  | -0.29σ       | -3.00%         |
| 13   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.767μs  | -0.55σ       | -5.58%         |
| 14   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.767μs  | -0.54σ       | -5.55%         |
| 15   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.991μs  | +0.62σ       | +6.39%         |
| 16   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.933μs  | +0.32σ       | +3.31%         |
| 17   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.737μs  | -0.70σ       | -7.15%         |
| 18   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.862μs  | -0.05σ       | -0.50%         |
| 19   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.979μs  | +0.56σ       | +5.77%         |
| 20   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.767μs  | -0.54σ       | -5.54%         |
| 21   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.722μs  | -0.78σ       | -7.95%         |
| 22   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.746μs  | -0.66σ       | -6.71%         |
| 23   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.798μs  | -0.38σ       | -3.93%         |
| 24   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.898μs  | +0.14σ       | +1.45%         |
| 25   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.338μs  | +2.44σ       | +24.97%        |
| 26   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.724μs  | -0.77σ       | -7.84%         |
| 27   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.713μs  | -0.82σ       | -8.43%         |
| 28   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.749μs  | -0.64σ       | -6.55%         |
| 29   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.944μs  | +0.38σ       | +3.88%         |
| 30   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.198μs  | +1.71σ       | +17.45%        |
| 31   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.749μs  | -0.64σ       | -6.53%         |
| 32   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.361μs  | +2.56σ       | +26.18%        |
| 33   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.792μs  | -0.41σ       | -4.21%         |
| 34   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.039μs  | +0.88σ       | +8.98%         |
| 35   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.687μs  | -0.96σ       | -9.84%         |
| 36   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.742μs  | -0.67σ       | -6.88%         |
| 37   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.258μs  | +2.02σ       | +20.68%        |
| 38   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.741μs  | -0.68σ       | -6.98%         |
| 39   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.740μs  | -0.69σ       | -7.03%         |
| 40   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.749μs  | -0.64σ       | -6.51%         |
| 41   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.719μs  | -0.79σ       | -8.12%         |
| 42   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.872μs  | +0.00σ       | +0.04%         |
| 43   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.129μs  | +1.35σ       | +13.77%        |
| 44   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.767μs  | -0.54σ       | -5.55%         |
| 45   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.211μs  | +1.78σ       | +18.19%        |
| 46   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.738μs  | -0.70σ       | -7.12%         |
| 47   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.719μs  | -0.80σ       | -8.13%         |
| 48   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.816μs  | -0.29σ       | -2.92%         |
| 49   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.699μs  | -0.90σ       | -9.20%         |
| 50   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.792μs  | -0.41σ       | -4.21%         |
| 51   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.829μs  | -0.22σ       | -2.26%         |
| 52   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.717μs  | -0.81σ       | -8.23%         |
| 53   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.732μs  | -0.73σ       | -7.46%         |
| 54   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.807μs  | -0.34σ       | -3.44%         |
| 55   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.267μs  | +2.07σ       | +21.14%        |
| 56   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.390μs  | +2.71σ       | +27.74%        |
| 57   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.786μs  | -0.44σ       | -4.54%         |
| 58   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.864μs  | -0.04σ       | -0.40%         |
| 59   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.715μs  | -0.82σ       | -8.34%         |
| 60   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.841μs  | -0.16σ       | -1.62%         |
| 61   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.694μs  | -0.92σ       | -9.45%         |
| 62   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.052μs  | +0.94σ       | +9.65%         |
| 63   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.729μs  | -0.74σ       | -7.62%         |
| 64   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.894μs  | +0.12σ       | +1.24%         |
| 65   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.016μs  | +0.75σ       | +7.72%         |
| 66   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.704μs  | -0.87σ       | -8.94%         |
| 67   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.394μs  | +2.73σ       | +27.95%        |
| 68   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.035μs  | +0.86σ       | +8.76%         |
| 69   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.939μs  | +0.35σ       | +3.61%         |
| 70   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.714μs  | -0.82σ       | -8.41%         |
| 71   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.073μs  | +1.06σ       | +10.80%        |
| 72   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.255μs  | +2.01σ       | +20.53%        |
| 73   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.806μs  | -0.34σ       | -3.51%         |
| 74   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.740μs  | -0.69σ       | -7.03%         |
| 75   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.713μs  | -0.83σ       | -8.46%         |
| 76   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.782μs  | -0.47σ       | -4.77%         |
| 77   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.763μs  | -0.56σ       | -5.76%         |
| 78   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.681μs  | -0.99σ       | -10.14%        |
| 79   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.708μs  | -0.85σ       | -8.74%         |
| 80   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.706μs  | -0.86σ       | -8.82%         |
| 81   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.764μs  | -0.56σ       | -5.72%         |
| 82   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.913μs  | +0.22σ       | +2.24%         |
| 83   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.763μs  | -0.56σ       | -5.75%         |
| 84   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.967μs  | +0.50σ       | +5.12%         |
| 85   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.750μs  | -0.63σ       | -6.49%         |
| 86   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.745μs  | -0.66σ       | -6.73%         |
| 87   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.299μs  | +2.24σ       | +22.89%        |
| 88   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.742μs  | -0.67σ       | -6.88%         |
| 89   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.986μs  | +0.60σ       | +6.16%         |
| 90   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.711μs  | -0.84σ       | -8.57%         |
| 91   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.735μs  | -0.71σ       | -7.30%         |
| 92   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.784μs  | -0.45σ       | -4.65%         |
| 93   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.284μs  | +2.16σ       | +22.08%        |
| 94   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.783μs  | -0.46σ       | -4.70%         |
| 95   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.075μs  | +1.06σ       | +10.87%        |
| 96   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.724μs  | -0.77σ       | -7.87%         |
| 97   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 2.360μs  | +2.56σ       | +26.13%        |
| 98   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.879μs  | +0.04σ       | +0.41%         |
| 99   | ReflectionVsClosureBindBench | benchClosureImplementation           |     | 100000 | 696,664b | 1.763μs  | -0.57σ       | -5.80%         |
| 0    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.377μs  | +2.99σ       | +34.24%        |
| 1    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.964μs  | -0.53σ       | -6.03%         |
| 2    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.002μs  | -0.20σ       | -2.31%         |
| 3    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.078μs  | +0.44σ       | +5.09%         |
| 4    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.951μs  | -0.64σ       | -7.30%         |
| 5    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.969μs  | -0.48σ       | -5.53%         |
| 6    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.999μs  | -0.23σ       | -2.62%         |
| 7    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.065μs  | +0.33σ       | +3.80%         |
| 8    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.390μs  | +3.10σ       | +35.52%        |
| 9    | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.973μs  | -0.45σ       | -5.14%         |
| 10   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.948μs  | -0.66σ       | -7.56%         |
| 11   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.985μs  | -0.35σ       | -4.01%         |
| 12   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.034μs  | +0.07σ       | +0.77%         |
| 13   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.952μs  | -0.63σ       | -7.22%         |
| 14   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.955μs  | -0.60σ       | -6.90%         |
| 15   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.947μs  | -0.67σ       | -7.70%         |
| 16   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.991μs  | -0.29σ       | -3.34%         |
| 17   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.016μs  | -0.08σ       | -0.97%         |
| 18   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.003μs  | -0.19σ       | -2.17%         |
| 19   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.943μs  | -0.70σ       | -8.05%         |
| 20   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.951μs  | -0.63σ       | -7.25%         |
| 21   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.958μs  | -0.58σ       | -6.64%         |
| 22   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.343μs  | +2.70σ       | +30.96%        |
| 23   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.969μs  | -0.48σ       | -5.49%         |
| 24   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.978μs  | -0.40σ       | -4.61%         |
| 25   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.954μs  | -0.61σ       | -6.99%         |
| 26   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.963μs  | -0.54σ       | -6.13%         |
| 27   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.108μs  | +0.70σ       | +7.98%         |
| 28   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.947μs  | -0.67σ       | -7.68%         |
| 29   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.989μs  | -0.31σ       | -3.59%         |
| 30   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.100μs  | +0.63σ       | +7.27%         |
| 31   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.385μs  | +3.06σ       | +35.01%        |
| 32   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.977μs  | -0.41σ       | -4.73%         |
| 33   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.955μs  | -0.60σ       | -6.92%         |
| 34   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.946μs  | -0.68σ       | -7.81%         |
| 35   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.945μs  | -0.69σ       | -7.85%         |
| 36   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.984μs  | -0.35σ       | -4.04%         |
| 37   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.966μs  | -0.51σ       | -5.84%         |
| 38   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.970μs  | -0.48σ       | -5.46%         |
| 39   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.940μs  | -0.73σ       | -8.31%         |
| 40   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.949μs  | -0.65σ       | -7.44%         |
| 41   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.369μs  | +2.92σ       | +33.46%        |
| 42   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.076μs  | +0.43σ       | +4.90%         |
| 43   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.973μs  | -0.45σ       | -5.17%         |
| 44   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.976μs  | -0.43σ       | -4.89%         |
| 45   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.978μs  | -0.40σ       | -4.63%         |
| 46   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.957μs  | -0.58σ       | -6.69%         |
| 47   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.982μs  | -0.37σ       | -4.25%         |
| 48   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.998μs  | -0.24σ       | -2.75%         |
| 49   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.985μs  | -0.35σ       | -4.00%         |
| 50   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.954μs  | -0.61σ       | -6.98%         |
| 51   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.052μs  | +0.23σ       | +2.58%         |
| 52   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.014μs  | -0.10σ       | -1.19%         |
| 53   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.971μs  | -0.47σ       | -5.35%         |
| 54   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.158μs  | +1.12σ       | +12.85%        |
| 55   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.979μs  | -0.40σ       | -4.54%         |
| 56   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.057μs  | +0.27σ       | +3.08%         |
| 57   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.293μs  | +2.28σ       | +26.07%        |
| 58   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.937μs  | -0.76σ       | -8.68%         |
| 59   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.372μs  | +2.95σ       | +33.72%        |
| 60   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.119μs  | +0.79σ       | +9.06%         |
| 61   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.966μs  | -0.51σ       | -5.84%         |
| 62   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.965μs  | -0.51σ       | -5.88%         |
| 63   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.944μs  | -0.69σ       | -7.95%         |
| 64   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.981μs  | -0.38σ       | -4.35%         |
| 65   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.961μs  | -0.55σ       | -6.35%         |
| 66   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.985μs  | -0.35σ       | -4.01%         |
| 67   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.958μs  | -0.57σ       | -6.56%         |
| 68   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.983μs  | -0.36σ       | -4.14%         |
| 69   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.968μs  | -0.49σ       | -5.65%         |
| 70   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.963μs  | -0.53σ       | -6.08%         |
| 71   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.202μs  | +1.51σ       | +17.23%        |
| 72   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.959μs  | -0.57σ       | -6.53%         |
| 73   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.954μs  | -0.61σ       | -7.04%         |
| 74   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.278μs  | +2.15σ       | +24.57%        |
| 75   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.987μs  | -0.33σ       | -3.77%         |
| 76   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.107μs  | +0.69σ       | +7.90%         |
| 77   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.023μs  | -0.02σ       | -0.23%         |
| 78   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.950μs  | -0.65σ       | -7.43%         |
| 79   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.950μs  | -0.65σ       | -7.40%         |
| 80   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.975μs  | -0.43σ       | -4.94%         |
| 81   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.031μs  | +0.04σ       | +0.47%         |
| 82   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.967μs  | -0.50σ       | -5.73%         |
| 83   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.199μs  | +1.48σ       | +16.89%        |
| 84   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.991μs  | -0.30σ       | -3.41%         |
| 85   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.938μs  | -0.75σ       | -8.59%         |
| 86   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.952μs  | -0.63σ       | -7.19%         |
| 87   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.982μs  | -0.37σ       | -4.28%         |
| 88   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.028μs  | +0.02σ       | +0.22%         |
| 89   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.952μs  | -0.63σ       | -7.17%         |
| 90   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.147μs  | +1.04σ       | +11.85%        |
| 91   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.039μs  | +0.12σ       | +1.32%         |
| 92   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.943μs  | -0.70σ       | -8.07%         |
| 93   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.034μs  | +0.07σ       | +0.84%         |
| 94   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.004μs  | -0.19σ       | -2.14%         |
| 95   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.972μs  | -0.46σ       | -5.21%         |
| 96   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.381μs  | +3.02σ       | +34.62%        |
| 97   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 1.026μs  | +0.00σ       | +0.03%         |
| 98   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.964μs  | -0.53σ       | -6.03%         |
| 99   | ReflectionVsClosureBindBench | benchBetterDesignedClassWithoutMagic |     | 100000 | 696,680b | 0.948μs  | -0.66σ       | -7.57%         |
+------+------------------------------+--------------------------------------+-----+--------+----------+----------+--------------+----------------+
```
