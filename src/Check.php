<?php

namespace Sensi\Codein;

use Generator;

abstract class Check
{
    /** @var string */
    protected $file;

    /** @var string */
    protected $code;

    protected function initialize(string $file) : void
    {
        $this->file = $file;
        $this->code = preg_replace("@/\*(.*?)\*/@ms", '', file_get_contents($file));
    }

    protected function extractClass(string $file) :? string
    {
        $this->initialize($file);
        $namespace = null;
        if (preg_match('@^namespace ([A-Za-z][A-Za-z0-9\\\\_]*);$@m', $this->code, $matches)) {
            $namespace = $matches[1];
        }
        $classname = null;
        if (preg_match('@^((final|abstract) )?class ([A-Za-z][A-Za-z0-9\\\\_]*)(\s|$)@m', $this->code, $matches)) {
            $classname = $matches[3];
        }
        if (!isset($classname)) {
            return null;
        }
        return isset($namespace) ? "$namespace\\$classname" : $classname;
    }

    public abstract function check(string $code) : Generator;
}

