<?php

namespace Sensi\Codein;

use Ansi;

class Walker
{
    /** @var Sensi\Codein\Namespaces */
    private $namespaces;

    public function __construct()
    {
        $this->namespaces = new Namespaces;
        $this->typehints = new Typehints;
        $this->doccomments = new Doccomments;
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
            foreach ([
                $this->namespaces,
                $this->typehints,
            ] as $errors) {
                foreach ($errors->check("$dir/$entry") as $error) {
                    fwrite(STDOUT, Ansi::tagsToColors("$error<reset>\n"));
                }
            }
        }
    }
}

