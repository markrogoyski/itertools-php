<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * Accumulates the mean of numbers added one at a time, without the sum overflowing.
 *
 * Shared by {@see \IterTools\Reduce::toAverage()} and {@see \IterTools\Math::runningAverage()}.
 * The sum is taken directly, so while it stays finite the mean is exactly $sum / $count, int when
 * the int sum divides evenly. Once a float sum of finite values overflows, as [PHP_FLOAT_MAX,
 * PHP_FLOAT_MAX] does although its mean is PHP_FLOAT_MAX, the sum is kept scaled by 2^-64 instead,
 * until it is back within range. Scaling by a power of two is exact, so the scaled sum rounds as the
 * unscaled one would with an unbounded exponent, and there is room for PHP_INT_MAX terms of
 * magnitude PHP_FLOAT_MAX. A term within 2^64 of the subnormal range loses low bits when scaled, but
 * only while the sum is past PHP_FLOAT_MAX, far above those bits; returning to direct summation once
 * the sum fits keeps a later small term, as in [MAX, MAX, -MAX, -MAX, 1e-310], from vanishing. A
 * genuine infinity or NAN among the values still makes the sum infinite or NAN, as the plain sum
 * would.
 */
final class MeanAccumulator
{
    /**
     * 2^-64, the scale of the sum once it has overflowed.
     */
    private const SCALE = 5.421010862427522E-20;

    private int $count = 0;
    private int|float $sum = 0;
    private bool $scaled = false;

    /**
     * @param int|float|null $initialValue (Optional) A first value, which seeds the sum as is rather
     *                                     than being added to 0, so that -0.0 keeps its sign.
     */
    public function __construct(int|float|null $initialValue = null)
    {
        if ($initialValue !== null) {
            $this->count = 1;
            $this->sum   = $initialValue;
        }
    }

    /**
     * An accumulator that continues from $sum, the plain sum of $count values, as a caller that sums directly
     * hands it over once the sum would overflow.
     *
     * @param int|float $sum   finite
     * @param int       $count
     *
     * @return self
     */
    public static function resume(int|float $sum, int $count): self
    {
        $accumulator        = new self();
        $accumulator->sum   = $sum;
        $accumulator->count = $count;

        return $accumulator;
    }

    /**
     * @param mixed $value a number, or a value PHP arithmetic coerces to one (null, bool, numeric string)
     */
    public function add(mixed $value): void
    {
        $this->count++;

        if ($this->scaled) {
            /**
             * @psalm-suppress MixedOperand
             * @phpstan-ignore binaryOp.invalid
             */
            $sum = (float) ($this->sum + $value * self::SCALE);

            // Back within range, the sum unscales exactly; NAN and INF stay scaled, which keeps them.
            if (\abs($sum) <= \PHP_FLOAT_MAX * self::SCALE) {
                $sum         /= self::SCALE;
                $this->scaled = false;
            }

            $this->sum = $sum;
            return;
        }

        /**
         * @psalm-suppress MixedOperand
         * @phpstan-ignore binaryOp.invalid
         */
        $sum = $this->sum + $value;

        // A finite sum turns infinite either by overflowing or by adding an infinity, which a finite
        // scaled term tells apart.
        if (\is_float($sum) && \is_infinite($sum) && \is_finite($this->sum)) {
            /**
             * @psalm-suppress MixedOperand
             * @phpstan-ignore binaryOp.invalid
             */
            $term = (float) ($value * self::SCALE);
            if (\is_finite($term)) {
                $sum = (float) $this->sum * self::SCALE + $term;
                $this->scaled = true;
            }
        }

        $this->sum = $sum;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * The mean of the values added so far, of which there must be at least one.
     *
     * @return int|float
     */
    public function mean(): int|float
    {
        /** @psalm-suppress InvalidOperand */
        $mean = $this->sum / $this->count;

        return $this->scaled ? (float) $mean / self::SCALE : $mean;
    }
}
