<?php
namespace Chargily\V2Bundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ChargilyV2Bundle extends AbstractBundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}
