<?php

namespace Sensi\Codein;

class Walker
{
    /** @var Sensi\Codein\Namespaces */
    private $namespaces;

    public function __construct()
    {
        $this->namespaces = new Namespaces;
    }

    public function walk(string $dir) : void
    {
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {
            if ($entry{0} == '.') {
                continue;
            }
            if (is_dir("$dir/$entry")) {
                $this->walk("$dir/$entry");
                continue;
            }
            if (!preg_match("@\.php$@", $entry)) {
                continue;
            }
            foreach ($this->namespaces->check("$dir/$entry") as $error) {
                if ($error instanceof Error) {
                }
            }
        }
    }
}

