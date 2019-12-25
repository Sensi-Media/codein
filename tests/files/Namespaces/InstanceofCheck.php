<?php

namespace Sensi\Codein\Test\Namespaces;

use Foo;

class ReturnTypehint
{
    public function foo($bar)
    {
        return $bar instanceof Foo;
    }
}

