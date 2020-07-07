<?php

namespace Store\Exceptions;

class CartException extends \Exception
{
    public static function productOutOfstock(): self
    {
        return new static('This product is out of stock');
    }

    public static function productNotEnoughStock(): self
    {
        return new static('This product does not have enough stock');
    }
}
