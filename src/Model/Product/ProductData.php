<?php

declare(strict_types=1);

namespace Linio\SellerCenter\Model\Product;

use JsonSerializable;
use Linio\SellerCenter\Contract\ProductConditionTypes;
use Linio\SellerCenter\Exception\InvalidDomainException;
use stdClass;

class ProductData implements JsonSerializable
{
    public const FEED_CONDITION_TYPE = 'ConditionType';
    public const FEED_PACKAGE_WEIGHT = 'PackageWeight';
    public const FEED_PACKAGE_HEIGHT = 'PackageHeight';
    public const FEED_PACKAGE_WIDTH = 'PackageWidth';
    public const FEED_PACKAGE_LENGTH = 'PackageLength';

    /**
     * @var mixed[]
     */
    protected $attributes = [];

    public function __construct(
        string $conditionType = null,
        float $packageHeight = null,
        float $packageWidth = null,
        float $packageLength = null,
        float $packageWeight = null
    ) {
        if ($conditionType !== null) {
            $this->validateConditionType($conditionType);
            $this->attributes[self::FEED_CONDITION_TYPE] = $conditionType;
        }

        if ($packageHeight !== null) {
            $this->validateGreaterThan(0, $packageHeight, self::FEED_PACKAGE_HEIGHT);
            $this->attributes[self::FEED_PACKAGE_HEIGHT] = $packageHeight;
        }

        if ($packageWidth !== null) {
            $this->validateGreaterThan(0, $packageWidth, self::FEED_PACKAGE_WIDTH);
            $this->attributes[self::FEED_PACKAGE_WIDTH] = $packageWidth;
        }

        if ($packageLength !== null) {
            $this->validateGreaterThan(0, $packageLength, self::FEED_PACKAGE_LENGTH);
            $this->attributes[self::FEED_PACKAGE_LENGTH] = $packageLength;
        }

        if ($packageWeight !== null) {
            $this->validateGreaterThan(0, $packageWeight, self::FEED_PACKAGE_WEIGHT);
            $this->attributes[self::FEED_PACKAGE_WEIGHT] = $packageWeight;
        }
    }

    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * @return mixed|null
     */
    public function getAttribute(string $attribute)
    {
        if (key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute];
        }

        return null;
    }

    public function add(string $name, $value): void
    {
        if (!key_exists($name, $this->attributes)) {
            $this->attributes[$name] = $value;
        }
    }

    public function jsonSerialize(): stdClass
    {
        $serialized = new stdClass();
        foreach ($this->attributes as $attribute => $value) {
            $serialized->$attribute = $value;
        }

        return $serialized;
    }

    private function validateConditionType(string $conditionType): void
    {
        if (!in_array($conditionType, ProductConditionTypes::CONDITION_TYPES)) {
            throw new InvalidDomainException('ConditionType');
        }
    }

    private function validateGreaterThan(int $minValue, float $value, string $feedName): void
    {
        if ($value < $minValue) {
            throw new InvalidDomainException($feedName);
        }
    }
}
