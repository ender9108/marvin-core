<?php

/*
 * This file is part of the webmozart/assert package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EnderLab\DddCqrsBundle\Domain\Assert;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use Countable;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use EnderLab\DddCqrsBundle\Domain\Exception\AssertException;
use Exception;
use InvalidArgumentException;
use ResourceBundle;
use SimpleXMLElement;
use Throwable;
use Traversable;

/**
 * Efficient assertions to validate the input/output of your methods.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class Assert
{
    use Mixin;

    /**
     * @psalm-pure
     *
     * @psalm-assert string $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function string(mixed $value, string $message = ''): void
    {
        if (!\is_string($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a string. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert non-empty-string $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function stringNotEmpty(mixed $value, string $message = ''): void
    {
        static::string($value, $message);
        static::notEq($value, '', $message);
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert int $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function integer(mixed $value, string $message = ''): void
    {
        if (!\is_int($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an integer. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert numeric $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function integerish(mixed $value, string $message = ''): void
    {
        if (!\is_numeric($value) || $value != (int) $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an integerish value. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert positive-int $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function positiveInteger(mixed $value, string $message = ''): void
    {
        if (!(\is_int($value) && $value > 0)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a positive integer. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert float $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function float(mixed $value, string $message = ''): void
    {
        if (!\is_float($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a float. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert numeric $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function numeric(mixed $value, string $message = ''): void
    {
        if (!\is_numeric($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a numeric. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert positive-int|0 $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function natural(mixed $value, string $message = ''): void
    {
        if (!\is_int($value) || $value < 0) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a non-negative integer. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert bool $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function boolean(mixed $value, string $message = ''): void
    {
        if (!\is_bool($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a boolean. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert scalar $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function scalar(mixed $value, string $message = ''): void
    {
        if (!\is_scalar($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a scalar. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert object $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function object(mixed $value, string $message = ''): void
    {
        if (!\is_object($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an object. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert resource $value
     *
     * @param mixed       $value
     * @param string|null $type    type of resource this should be. @see https://www.php.net/manual/en/function.get-resource-type.php
     * @param string      $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function resource(mixed $value, ?string $type = null, string $message = ''): void
    {
        if (!\is_resource($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a resource. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value), 'expectedType' => $type]
            );
        }

        if ($type && $type !== \get_resource_type($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a resource of type %2$s. Got: %s', static::typeToString($value), $type),
                ['type' => static::typeToString($value), 'expectedType' => $type]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert callable $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isCallable(mixed $value, string $message = ''): void
    {
        if (!\is_callable($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a callable. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert array $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isArray(mixed $value, string $message = ''): void
    {
        if (!\is_array($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an array. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert iterable $value
     *
     * @deprecated use "isIterable" or "isInstanceOf" instead
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isTraversable(mixed $value, string $message = ''): void
    {
        @\trigger_error(
            \sprintf(
                'The "%s" assertion is deprecated. You should stop using it, as it will soon be removed in 2.0 version. Use "isIterable" or "isInstanceOf" instead.',
                __METHOD__
            ),
            \E_USER_DEPRECATED
        );

        if (!\is_array($value) && !($value instanceof Traversable)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a traversable. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert array|ArrayAccess $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isArrayAccessible(mixed $value, string $message = ''): void
    {
        if (!\is_array($value) && !($value instanceof ArrayAccess)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an array accessible. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert countable $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isCountable(mixed $value, string $message = ''): void
    {
        if (
            !\is_array($value)
            && !($value instanceof Countable)
            && !($value instanceof ResourceBundle)
            && !($value instanceof SimpleXMLElement)
        ) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a countable. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert iterable $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isIterable(mixed $value, string $message = ''): void
    {
        if (!\is_array($value) && !($value instanceof Traversable)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an iterable. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-template ExpectedType of object
     *
     * @psalm-param class-string<ExpectedType> $class
     *
     * @psalm-assert ExpectedType $value
     *
     * @param mixed         $value
     * @param string|object $class
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isInstanceOf(mixed $value, string|object $class, string $message = ''): void
    {
        if (!($value instanceof $class)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an instance of %2$s. Got: %s', static::typeToString($value), $class),
                ['type' => static::typeToString($value), 'class' => $class]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-template ExpectedType of object
     *
     * @psalm-param class-string<ExpectedType> $class
     *
     * @psalm-assert !ExpectedType $value
     *
     * @param mixed         $value
     * @param string|object $class
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notInstanceOf(mixed $value, string|object $class, string $message = ''): void
    {
        if ($value instanceof $class) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an instance other than %2$s. Got: %s', static::typeToString($value), $class),
                ['type' => static::typeToString($value), 'class' => $class]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-param array<class-string> $classes
     *
     * @param mixed                $value
     * @param array<object|string> $classes
     * @param string               $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isInstanceOfAny(mixed $value, array $classes, string $message = ''): void
    {
        foreach ($classes as $class) {
            if ($value instanceof $class) {
                return;
            }
        }

        static::reportInvalidArgument(
            $message ?: sprintf('Expected an instance of any of %2$s. Got: %s', static::typeToString($value), \implode(', ', \array_map(array(static::class, 'valueToString'), $classes))),
            ['type' => static::typeToString($value), 'classes' => \implode(', ', \array_map(array(static::class, 'valueToString'), $classes))]
        );
    }

    /**
     * @psalm-pure
     *
     * @psalm-template ExpectedType of object
     *
     * @psalm-param class-string<ExpectedType> $class
     *
     * @psalm-assert ExpectedType|class-string<ExpectedType> $value
     *
     * @param object|string $value
     * @param string        $class
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isAOf(object|string $value, string $class, string $message = ''): void
    {
        static::string($class, 'Expected class as a string. Got: %s');

        if (!\is_a($value, $class, \is_string($value))) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an instance of this class or to this class among its parents "%2$s". Got: %s', static::valueToString($value), $class),
                ['value' => static::valueToString($value), 'class' => $class]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-template UnexpectedType of object
     *
     * @psalm-param class-string<UnexpectedType> $class
     *
     * @psalm-assert !UnexpectedType $value
     * @psalm-assert !class-string<UnexpectedType> $value
     *
     * @param object|string $value
     * @param string        $class
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isNotA(object|string $value, string $class, string $message = ''): void
    {
        static::string($class, 'Expected class as a string. Got: %s');

        if (\is_a($value, $class, \is_string($value))) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an instance of this class or to this class among its parents other than "%2$s". Got: %s', static::valueToString($value), $class),
                ['value' => static::valueToString($value), 'class' => $class]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-param array<class-string> $classes
     *
     * @param object|string $value
     * @param string[]      $classes
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isAnyOf(object|string $value, array $classes, string $message = ''): void
    {
        foreach ($classes as $class) {
            static::string($class, 'Expected class as a string. Got: %s');

            if (\is_a($value, $class, \is_string($value))) {
                return;
            }
        }

        static::reportInvalidArgument(
            $message ?: sprintf('Expected an instance of any of this classes or any of those classes among their parents "%2$s". Got: %s', static::valueToString($value), \implode(', ', $classes)),
            ['value' => static::valueToString($value), 'classes' => \implode(', ', $classes)]
        );
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert empty $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isEmpty(mixed $value, string $message = ''): void
    {
        if (!empty($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an empty value. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert !empty $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notEmpty(mixed $value, string $message = ''): void
    {
        if (empty($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a non-empty value. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert null $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function null(mixed $value, string $message = ''): void
    {
        if (null !== $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected null. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert !null $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notNull(mixed $value, string $message = ''): void
    {
        if (null === $value) {
            static::reportInvalidArgument(
                $message ?: 'Expected a value other than null.',
                []
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert true $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function true(mixed $value, string $message = ''): void
    {
        if (true !== $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to be true. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert false $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function false(mixed $value, string $message = ''): void
    {
        if (false !== $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to be false. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert !false $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notFalse(mixed $value, string $message = ''): void
    {
        if (false === $value) {
            static::reportInvalidArgument(
                $message ?: 'Expected a value other than false.',
                []
            );
        }
    }

    /**
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function ip(mixed $value, string $message = ''): void
    {
        if (false === \filter_var($value, \FILTER_VALIDATE_IP)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to be an IP. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function ipv4(mixed $value, string $message = ''): void
    {
        if (false === \filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to be an IPv4. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function ipv6(mixed $value, string $message = ''): void
    {
        if (false === \filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to be an IPv6. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function email(mixed $value, string $message = ''): void
    {
        if (false === \filter_var($value, FILTER_VALIDATE_EMAIL)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to be a valid e-mail address. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * Does non strict comparisons on the items, so ['3', 3] will not pass the assertion.
     *
     * @param array  $values
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function uniqueValues(array $values, string $message = ''): void
    {
        $allValues = \count($values);
        $uniqueValues = \count(\array_unique($values));

        if ($allValues !== $uniqueValues) {
            $difference = $allValues - $uniqueValues;

            static::reportInvalidArgument(
                $message ?: sprintf('Expected an array of unique values, but %s of them %s duplicated', $difference, 1 === $difference ? 'is' : 'are'),
                ['difference' => $difference, 'verb' => 1 === $difference ? 'is' : 'are']
            );
        }
    }

    /**
     * @param mixed  $value
     * @param mixed  $expect
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function eq(mixed $value, mixed $expect, string $message = ''): void
    {
        if ($expect != $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value equal to %2$s. Got: %s', static::valueToString($value), static::valueToString($expect)),
                ['value' => static::valueToString($value), 'expect' => static::valueToString($expect)]
            );
        }
    }

    /**
     * @param mixed  $value
     * @param mixed  $expect
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notEq(mixed $value, mixed $expect, string $message = ''): void
    {
        if ($expect == $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a different value than %s.', static::valueToString($expect)),
                ['expect' => static::valueToString($expect)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param mixed  $expect
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function same(mixed $value, mixed $expect, string $message = ''): void
    {
        if ($expect !== $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value identical to %2$s. Got: %s', static::valueToString($value), static::valueToString($expect)),
                ['value' => static::valueToString($value), 'expect' => static::valueToString($expect)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param mixed  $expect
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notSame(mixed $value, mixed $expect, string $message = ''): void
    {
        if ($expect === $value) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value not identical to %s.', static::valueToString($expect)),
                ['expect' => static::valueToString($expect)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param mixed  $limit
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function greaterThan(mixed $value, mixed $limit, string $message = ''): void
    {
        if ($value <= $limit) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value greater than %2$s. Got: %s', static::valueToString($value), static::valueToString($limit)),
                ['value' => static::valueToString($value), 'limit' => static::valueToString($limit)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param mixed  $limit
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function greaterThanEq(mixed $value, mixed $limit, string $message = ''): void
    {
        if ($value < $limit) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value greater than or equal to %2$s. Got: %s', static::valueToString($value), static::valueToString($limit)),
                ['value' => static::valueToString($value), 'limit' => static::valueToString($limit)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param mixed  $limit
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function lessThan(mixed $value, mixed $limit, string $message = ''): void
    {
        if ($value >= $limit) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value less than %2$s. Got: %s', static::valueToString($value), static::valueToString($limit)),
                ['value' => static::valueToString($value), 'limit' => static::valueToString($limit)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param mixed  $limit
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function lessThanEq(mixed $value, mixed $limit, string $message = ''): void
    {
        if ($value > $limit) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value less than or equal to %2$s. Got: %s', static::valueToString($value), static::valueToString($limit)),
                ['value' => static::valueToString($value), 'limit' => static::valueToString($limit)]
            );
        }
    }

    /**
     * Inclusive range, so Assert::(3, 3, 5) passes.
     *
     * @psalm-pure
     *
     * @param mixed  $value
     * @param mixed  $min
     * @param mixed  $max
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function range(mixed $value, mixed $min, mixed $max, string $message = ''): void
    {
        if ($value < $min || $value > $max) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value between %2$s and %3$s. Got: %s', static::valueToString($value), static::valueToString($min), static::valueToString($max)),
                ['value' => static::valueToString($value), 'min' => static::valueToString($min), 'max' => static::valueToString($max)]
            );
        }
    }

    /**
     * A more human-readable alias of Assert::inArray().
     *
     * @psalm-pure
     *
     * @param mixed  $value
     * @param array  $values
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function oneOf(mixed $value, array $values, string $message = ''): void
    {
        static::inArray($value, $values, $message);
    }

    /**
     * Does strict comparison, so Assert::inArray(3, ['3']) does not pass the assertion.
     *
     * @psalm-pure
     *
     * @param mixed  $value
     * @param array  $values
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function inArray(mixed $value, array $values, string $message = ''): void
    {
        if (!\in_array($value, $values, true)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected one of: %2$s. Got: %s', static::valueToString($value), \implode(', ', \array_map(array(static::class, 'valueToString'), $values))),
                ['value' => static::valueToString($value), 'values' => \implode(', ', \array_map(array(static::class, 'valueToString'), $values))]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $subString
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function contains(string $value, string $subString, string $message = ''): void
    {
        if (false === \strpos($value, $subString)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain %2$s. Got: %s', static::valueToString($value), static::valueToString($subString)),
                ['value' => static::valueToString($value), 'subString' => static::valueToString($subString)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $subString
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notContains(string $value, string $subString, string $message = ''): void
    {
        if (false !== \strpos($value, $subString)) {
            static::reportInvalidArgument(
                $message ?: sprintf('%2$s was not expected to be contained in a value. Got: %s', static::valueToString($value), static::valueToString($subString)),
                ['value' => static::valueToString($value), 'subString' => static::valueToString($subString)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notWhitespaceOnly(string $value, string $message = ''): void
    {
        if (\preg_match('/^\s*$/', $value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a non-whitespace string. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $prefix
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function startsWith(string $value, string $prefix, string $message = ''): void
    {
        if (0 !== \strpos($value, $prefix)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to start with %2$s. Got: %s', static::valueToString($value), static::valueToString($prefix)),
                ['value' => static::valueToString($value), 'prefix' => static::valueToString($prefix)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $prefix
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notStartsWith(string $value, string $prefix, string $message = ''): void
    {
        if (0 === \strpos($value, $prefix)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value not to start with %2$s. Got: %s', static::valueToString($value), static::valueToString($prefix)),
                ['value' => static::valueToString($value), 'prefix' => static::valueToString($prefix)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function startsWithLetter(mixed $value, string $message = ''): void
    {
        static::string($value);

        $valid = isset($value[0]);

        if ($valid) {
            $locale = \setlocale(LC_CTYPE, 0);
            \setlocale(LC_CTYPE, 'C');
            $valid = \ctype_alpha($value[0]);
            \setlocale(LC_CTYPE, $locale);
        }

        if (!$valid) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to start with a letter. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $suffix
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function endsWith($value, $suffix, $message = '')
    {
        if ($suffix !== \substr($value, -\strlen($suffix))) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to end with %2$s. Got: %s', static::valueToString($value), static::valueToString($suffix)),
                ['value' => static::valueToString($value), 'suffix' => static::valueToString($suffix)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $suffix
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notEndsWith($value, $suffix, $message = '')
    {
        if ($suffix === \substr($value, -\strlen($suffix))) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value not to end with %2$s. Got: %s', static::valueToString($value), static::valueToString($suffix)),
                ['value' => static::valueToString($value), 'suffix' => static::valueToString($suffix)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $pattern
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function regex($value, $pattern, $message = '')
    {
        if (!\preg_match($pattern, $value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('The value %s does not match the expected pattern.', static::valueToString($value)),
                ['value' => static::valueToString($value), 'pattern' => $pattern]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $pattern
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function notRegex($value, $pattern, $message = '')
    {
        if (\preg_match($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
            static::reportInvalidArgument(
                $message ?: sprintf('The value %s matches the pattern %s (at offset %d).', static::valueToString($value), static::valueToString($pattern), $matches[0][1]),
                ['value' => static::valueToString($value), 'pattern' => static::valueToString($pattern), 'offset' => $matches[0][1]]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function unicodeLetters($value, $message = '')
    {
        static::string($value);

        if (!\preg_match('/^\p{L}+$/u', $value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain only Unicode letters. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function alpha($value, $message = '')
    {
        static::string($value);

        $locale = \setlocale(LC_CTYPE, 0);
        \setlocale(LC_CTYPE, 'C');
        $valid = !\ctype_alpha($value);
        \setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain only letters. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function digits($value, $message = '')
    {
        static::string($value);

        $locale = \setlocale(LC_CTYPE, 0);
        \setlocale(LC_CTYPE, 'C');
        $valid = !\ctype_digit($value);
        \setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain digits only. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function alnum($value, $message = '')
    {
        static::string($value);

        $locale = \setlocale(LC_CTYPE, 0);
        \setlocale(LC_CTYPE, 'C');
        $valid = !\ctype_alnum($value);
        \setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain letters and digits only. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert lowercase-string $value
     *
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function lower($value, $message = '')
    {
        static::string($value);

        $locale = \setlocale(LC_CTYPE, 0);
        \setlocale(LC_CTYPE, 'C');
        $valid = !\ctype_lower($value);
        \setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain lowercase characters only. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert !lowercase-string $value
     *
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function upper($value, $message = '')
    {
        static::string($value);

        $locale = \setlocale(LC_CTYPE, 0);
        \setlocale(LC_CTYPE, 'C');
        $valid = !\ctype_upper($value);
        \setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain uppercase characters only. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param int    $length
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function length($value, $length, $message = '')
    {
        if ($length !== static::strlen($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain %2$s characters. Got: %s', static::valueToString($value), $length),
                ['value' => static::valueToString($value), 'length' => $length]
            );
        }
    }

    /**
     * Inclusive min.
     *
     * @psalm-pure
     *
     * @param string    $value
     * @param int|float $min
     * @param string    $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function minLength($value, $min, $message = '')
    {
        if (static::strlen($value) < $min) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain at least %2$s characters. Got: %s', static::valueToString($value), $min),
                ['value' => static::valueToString($value), 'min' => $min]
            );
        }
    }

    /**
     * Inclusive max.
     *
     * @psalm-pure
     *
     * @param string    $value
     * @param int|float $max
     * @param string    $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function maxLength($value, $max, $message = '')
    {
        if (static::strlen($value) > $max) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain at most %2$s characters. Got: %s', static::valueToString($value), $max),
                ['value' => static::valueToString($value), 'max' => $max]
            );
        }
    }

    /**
     * Inclusive , so Assert::lengthBetween('asd', 3, 5); passes the assertion.
     *
     * @psalm-pure
     *
     * @param string    $value
     * @param int|float $min
     * @param int|float $max
     * @param string    $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function lengthBetween($value, $min, $max, $message = ''): void
    {
        $length = static::strlen($value);

        if ($length < $min || $length > $max) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a value to contain between %2$s and %3$s characters. Got: %s', static::valueToString($value), $min, $max),
                ['value' => static::valueToString($value), 'min' => $min, 'max' => $max]
            );
        }
    }

    /**
     * Will also pass if $value is a directory, use Assert::file() instead if you need to be sure it is a file.
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function fileExists($value, $message = '')
    {
        if (!\file_exists($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('The path %s does not exist.', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function file($value, $message = '')
    {
        if (!\is_file($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('The path %s is not a file.', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function directory($value, $message = '')
    {
        if (!\is_dir($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('The path %s is not a directory.', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function readable($value, $message = '')
    {
        if (!\is_readable($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('The path %s is not readable.', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function writable($value, $message = '')
    {
        if (!\is_writable($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('The path %s is not writable.', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-assert class-string $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function classExists($value, $message = '')
    {
        if (!\class_exists($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an existing class name. Got: %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-template ExpectedType of object
     *
     * @psalm-param class-string<ExpectedType> $class
     *
     * @psalm-assert class-string<ExpectedType>|ExpectedType $value
     *
     * @param mixed         $value
     * @param string|object $class
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function subclassOf($value, $class, $message = '')
    {
        if (!\is_subclass_of($value, $class)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected a sub-class of %2$s. Got: %s', static::valueToString($value), static::valueToString($class)),
                ['value' => static::valueToString($value), 'class' => static::valueToString($class)]
            );
        }
    }

    /**
     * @psalm-assert class-string $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function interfaceExists($value, $message = '')
    {
        if (!\interface_exists($value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an existing interface name. got %s', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-template ExpectedType of object
     *
     * @psalm-param class-string<ExpectedType> $interface
     *
     * @psalm-assert class-string<ExpectedType>|ExpectedType $value
     *
     * @param mixed  $value
     * @param mixed  $interface
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function implementsInterface($value, $interface, $message = '')
    {
        if (!\in_array($interface, \class_implements($value))) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an implementation of %2$s. Got: %s', static::valueToString($value), static::valueToString($interface)),
                ['value' => static::valueToString($value), 'interface' => static::valueToString($interface)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-param class-string|object $classOrObject
     *
     * @param string|object $classOrObject
     * @param mixed         $property
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function propertyExists($classOrObject, $property, $message = '')
    {
        if (!\property_exists($classOrObject, $property)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected the property %s to exist.', static::valueToString($property)),
                ['property' => static::valueToString($property)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-param class-string|object $classOrObject
     *
     * @param string|object $classOrObject
     * @param mixed         $property
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function propertyNotExists($classOrObject, $property, $message = '')
    {
        if (\property_exists($classOrObject, $property)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected the property %s to not exist.', static::valueToString($property)),
                ['property' => static::valueToString($property)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-param class-string|object $classOrObject
     *
     * @param string|object $classOrObject
     * @param mixed         $method
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function methodExists($classOrObject, $method, $message = '')
    {
        if (!(\is_string($classOrObject) || \is_object($classOrObject)) || !\method_exists($classOrObject, $method)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected the method %s to exist.', static::valueToString($method)),
                ['method' => static::valueToString($method)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-param class-string|object $classOrObject
     *
     * @param string|object $classOrObject
     * @param mixed         $method
     * @param string        $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function methodNotExists($classOrObject, $method, $message = '')
    {
        if ((\is_string($classOrObject) || \is_object($classOrObject)) && \method_exists($classOrObject, $method)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected the method %s to not exist.', static::valueToString($method)),
                ['method' => static::valueToString($method)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param array      $array
     * @param string|int $key
     * @param string     $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function keyExists($array, $key, $message = '')
    {
        if (!(isset($array[$key]) || \array_key_exists($key, $array))) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected the key %s to exist.', static::valueToString($key)),
                ['key' => static::valueToString($key)]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @param array      $array
     * @param string|int $key
     * @param string     $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function keyNotExists($array, $key, $message = '')
    {
        if (isset($array[$key]) || \array_key_exists($key, $array)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected the key %s to not exist.', static::valueToString($key)),
                ['key' => static::valueToString($key)]
            );
        }
    }

    /**
     * Checks if a value is a valid array key (int or string).
     *
     * @psalm-pure
     *
     * @psalm-assert array-key $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function validArrayKey($value, $message = '')
    {
        if (!(\is_int($value) || \is_string($value))) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected string or integer. Got: %s', static::typeToString($value)),
                ['type' => static::typeToString($value)]
            );
        }
    }

    /**
     * Does not check if $array is countable, this can generate a warning on php versions after 7.2.
     *
     * @param Countable|array $array
     * @param int             $number
     * @param string          $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function count($array, $number, $message = '')
    {
        static::eq(
            \count($array),
            $number,
            \sprintf(
                $message ?: 'Expected an array to contain %d elements. Got: %d.',
                $number,
                \count($array)
            )
        );
    }

    /**
     * Does not check if $array is countable, this can generate a warning on php versions after 7.2.
     *
     * @param Countable|array $array
     * @param int|float       $min
     * @param string          $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function minCount($array, $min, $message = '')
    {
        if (\count($array) < $min) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an array to contain at least %2$d elements. Got: %d', \count($array), $min),
                ['count' => \count($array), 'min' => $min]
            );
        }
    }

    /**
     * Does not check if $array is countable, this can generate a warning on php versions after 7.2.
     *
     * @param Countable|array $array
     * @param int|float       $max
     * @param string          $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function maxCount($array, $max, $message = '')
    {
        if (\count($array) > $max) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an array to contain at most %2$d elements. Got: %d', \count($array), $max),
                ['count' => \count($array), 'max' => $max]
            );
        }
    }

    /**
     * Does not check if $array is countable, this can generate a warning on php versions after 7.2.
     *
     * @param Countable|array $array
     * @param int|float       $min
     * @param int|float       $max
     * @param string          $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function countBetween($array, $min, $max, $message = '')
    {
        $count = \count($array);

        if ($count < $min || $count > $max) {
            static::reportInvalidArgument(
                $message ?: sprintf('Expected an array to contain between %2$d and %3$d elements. Got: %d', $count, $min, $max),
                ['count' => $count, 'min' => $min, 'max' => $max]
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert list $array
     *
     * @param mixed  $array
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isList($array, $message = '')
    {
        if (!\is_array($array)) {
            static::reportInvalidArgument(
                $message ?: 'Expected list - non-associative array.',
                []
            );
        }

        if (\function_exists('array_is_list')) {
            if (!\array_is_list($array)) {
                static::reportInvalidArgument(
                    $message ?: 'Expected list - non-associative array.',
                    []
                );
            }

            return;
        }

        if (array() === $array) {
            return;
        }

        $keys = array_keys($array);
        if (array_keys($keys) !== $keys) {
            static::reportInvalidArgument(
                $message ?: 'Expected list - non-associative array.',
                []
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-assert non-empty-list $array
     *
     * @param mixed  $array
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isNonEmptyList($array, $message = '')
    {
        static::isList($array, $message);
        static::notEmpty($array, $message);
    }

    /**
     * @psalm-pure
     *
     * @psalm-template T
     *
     * @psalm-param mixed|array<T> $array
     *
     * @psalm-assert array<string, T> $array
     *
     * @param mixed  $array
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isMap($array, $message = '')
    {
        if (
            !\is_array($array)
            || \array_keys($array) !== \array_filter(\array_keys($array), '\is_string')
        ) {
            static::reportInvalidArgument(
                $message ?: 'Expected map - associative array with string keys.',
                []
            );
        }
    }

    /**
     * @psalm-pure
     *
     * @psalm-template T
     *
     * @psalm-param mixed|array<T> $array
     *
     * @psalm-assert array<string, T> $array
     * @psalm-assert !empty $array
     *
     * @param mixed  $array
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function isNonEmptyMap($array, $message = '')
    {
        static::isMap($array, $message);
        static::notEmpty($array, $message);
    }

    /**
     * @psalm-pure
     *
     * @param string $value
     * @param string $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function uuid($value, $message = '')
    {
        $value = \str_replace(array('urn:', 'uuid:', '{', '}'), '', $value);

        // The nil UUID is special form of UUID that is specified to have all
        // 128 bits set to zero.
        if ('00000000-0000-0000-0000-000000000000' === $value) {
            return;
        }

        if (!\preg_match('/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/D', $value)) {
            static::reportInvalidArgument(
                $message ?: sprintf('Value %s is not a valid UUID.', static::valueToString($value)),
                ['value' => static::valueToString($value)]
            );
        }
    }

    /**
     * @psalm-param class-string<Throwable> $class
     *
     * @param Closure $expression
     * @param string  $class
     * @param string  $message
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public static function throws(Closure $expression, $class = 'Exception', $message = '')
    {
        static::string($class);

        $actual = 'none';

        try {
            $expression();
        } catch (Exception $e) {
            $actual = \get_class($e);
            if ($e instanceof $class) {
                return;
            }
        } catch (Throwable $e) {
            $actual = \get_class($e);
            if ($e instanceof $class) {
                return;
            }
        }

        static::reportInvalidArgument(
            $message ?: sprintf('Expected to throw "%s", got "%s"', $class, $actual),
            ['class' => $class, 'actual' => $actual]
        );
    }

    public static function isValidTimezone(string $value, string $message = ''): void
    {
        if (!\in_array($value, DateTimeZone::listIdentifiers(), true)) {
            static::reportInvalidArgument(
                $message ?: 'assert.expected_valid_timezone',
                ['%value%' => $value],
                'A0021'
            );
        }
    }

    /**
     * @throws BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        if ('nullOr' === \substr($name, 0, 6)) {
            if (null !== $arguments[0]) {
                $method = \lcfirst(\substr($name, 6));
                \call_user_func_array(array(static::class, $method), $arguments);
            }

            return;
        }

        if ('all' === \substr($name, 0, 3)) {
            static::isIterable($arguments[0]);

            $method = \lcfirst(\substr($name, 3));
            $args = $arguments;

            foreach ($arguments[0] as $entry) {
                $args[0] = $entry;

                \call_user_func_array(array(static::class, $method), $args);
            }

            return;
        }

        throw new BadMethodCallException('No such method: '.$name);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected static function valueToString($value)
    {
        if (null === $value) {
            return 'null';
        }

        if (true === $value) {
            return 'true';
        }

        if (false === $value) {
            return 'false';
        }

        if (\is_array($value)) {
            return 'array';
        }

        if (\is_object($value)) {
            if (\method_exists($value, '__toString')) {
                return \get_class($value).': '.self::valueToString($value->__toString());
            }

            if ($value instanceof DateTime || $value instanceof DateTimeImmutable) {
                return \get_class($value).': '.self::valueToString($value->format('c'));
            }

            if (\function_exists('enum_exists') && \enum_exists(\get_class($value))) {
                return \get_class($value).'::'.$value->name;
            }

            return \get_class($value);
        }

        if (\is_resource($value)) {
            return 'resource';
        }

        if (\is_string($value)) {
            return '"'.$value.'"';
        }

        return (string) $value;
    }

    /**
     * @psalm-pure
     *
     * @param mixed $value
     *
     * @return string
     */
    protected static function typeToString($value)
    {
        return \is_object($value) ? \get_class($value) : \gettype($value);
    }

    protected static function strlen($value)
    {
        if (!\function_exists('mb_detect_encoding')) {
            return \strlen($value);
        }

        if (false === $encoding = \mb_detect_encoding($value)) {
            return \strlen($value);
        }

        return \mb_strlen($value, $encoding);
    }

    /**
     * @param string $message
     *
     * @throws InvalidArgumentException
     *
     * @psalm-pure this method is not supposed to perform side-effects
     *
     * @psalm-return never
     */
    protected static function reportInvalidArgument(string $message, array $parameters = [])
    {
        throw new AssertException($message, $parameters);
    }

    private function __construct()
    {
    }
}
