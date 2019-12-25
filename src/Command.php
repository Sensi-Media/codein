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

    /**
     * For the specified $dir, traverse all PHP files and subdirectories to
     * analyze the code contained.
     *
     * @param string $dir
     * @return void
     */
    public function __invoke(string $dir) : void
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

    /**
     * Recursively walk and check directories.
     *
     * @param string $dir
     * @return int|null The number of errors found, or null if no tests were
     *  defined (see the options for the command).
     */
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
                foreach ($errors->check("$dir/$entry") as $error) {
                    ++$errs;
                    fwrite(STDOUT, Ansi::tagsToColors("$error<reset>\n"));
                }
            }
        }
        return $errs;
    }
}

