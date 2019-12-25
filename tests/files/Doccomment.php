<?php

namespace Sensi\Codein\Test;

class Doccomment
{
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

