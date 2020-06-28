<?php

namespace Store\Exceptions;

class CartException extends \Exception
{
    public static function productOutOfstock(): self
    {
        return new static('El producto está fuera de stock');
    }

    public static function productNotEnoughStock(): self
    {
        return new static('El producto no tiene suficiente stock');
    }
}
