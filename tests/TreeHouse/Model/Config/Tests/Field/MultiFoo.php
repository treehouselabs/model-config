<?php

namespace TreeHouse\Model\Config\Tests\Field;

use TreeHouse\Model\Config\Field\Enum;

class MultiFoo extends Enum
{
    const FOOZ = 1;
    const BARZ = 2;
    const BAZZ = 3;

    protected static $multiValued = true;
}