<?php

namespace App\Utility;

use Pimple\Container;
use Psr\Container\ContainerInterface;

class Psr11Container extends Container implements ContainerInterface
{
    public function get($id)
    {
        return $this[$id];
    }

    public function has($id)
    {
        return isset($this[$id]);
    }
}