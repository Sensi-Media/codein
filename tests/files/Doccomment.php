<?php

namespace Sensi\Codein\Test;

class Doccomment
{
    /**
     * This property has a doccomment, so it isn't flagged.
     */
    public $foo;

    public $bar;

    public function bar()
    {
    }

    /**
     * This method has a doccomment, so it isn't flagged.
     */
    public function _foo()
    {
    }
}

