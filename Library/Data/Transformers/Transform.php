<?php

namespace DigitalGrease\Library\Data\Transformers;

interface Transform
{
    public static function transform(string $value): string;
}
