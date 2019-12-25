<?php

namespace Sensi\Codein;

use Monolyth\Cliff;
use Ansi;

class Command extends Cliff\Command
{
    /** @var bool */
    public $namespaces = false;

    /** @var bool */
    public $typehints = false;

    /** @var bool */
    public $doccomments = false;

    /** @var bool */
    public $all = false;

    public function __invoke(string $dir)
    {
        $errs = $this->walk($dir);
        if (isset($errs)) {
            if (!$errs) {
                fwrite(STDOUT, Ansi::tagsToColors("<green>Everything okay!<reset>\n"));
            } elseif ($errs === 1) {
                fwrite(STDOUT, Ansi::tagsToColors("\n<reset>Found <bold>$errs <reset>code smell.\n"));
            } else {
                fwrite(STDOUT, Ansi::tagsToColors("\n<reset>Found <bold>$errs <reset>code smells.\n"));
            }
            fwrite(STDOUT, "\n");
        }
    }

    private function walk(string $dir) :? int
    {
        static $checks;
        if (!isset($checks)) {
            $checks = [];
            if ($this->namespaces || $this->all) {
                $checks[] = new Namespaces;
            }
            if ($this->typehints || $this->all) {
                $checks[] = new Typehints;
            }
            if ($this->doccomments || $this->all) {
                $checks[] = new Doccomments;
            }
        }
        if (!$checks) {
            fwrite(STDERR, Ansi::tagsToColors("\n<red>No checks specified!<reset>\n\n"));
            return null;
        }
        $errs = 0;
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {
            if ($entry{0} == '.') {
                continue;
            }
            if (is_dir("$dir/$entry")) {
                $errs += $this->walk("$dir/$entry");
                continue;
            }
            if (!preg_match("@\.php$@", $entry)) {
                continue;
            }
            foreach ($checks as $errors) {
                foreach ($errors->check(file_get_contents("$dir/$entry")) as $error) {
                    ++$errs;
                    fwrite(STDOUT, Ansi::tagsToColors("$error<reset>\n"));
                }
            }
        }
        return $errs;
    }
}

