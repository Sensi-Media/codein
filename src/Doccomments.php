<?php

namespace Sensi\Codein;

use ReflectionClass;
use Generator;

/**
 * Check if the class and all its methods have a doccomment.
 * TODO: also check this for properties...
 */
class Doccomments extends Check
{
    /**
     * Run the check.
     *
     * @param string $file
     * @return Generator
     */
    public function check(string $file) : Generator
    {
        if (!($class = $this->extractClass($file))) {
            return;
        }
        $reflection = new ReflectionClass($class);
        if (!$reflection->getDocComment()) {
            yield "<red>Class <darkRed>$class <red>is missing doccomment in <darkRed>{$this->file}";
        }
        foreach ($reflection->getMethods() as $method) {
            if ($method->getDeclaringClass()->name != $class) {
                continue;
            }
            if ($method->getFileName() != $reflection->getFileName()) {
                continue;
            }
            if (!$method->getDocComment()) {
                yield "<red>Method <darkRed>$class::{$method->name} <red>is missing doccomment in <darkRed>{$this->file}";
            }
        }
        foreach ($reflection->getProperties() as $property) {
            if (!$property->getDocComment()) {
                yield "<red>Property <darkRed>$class->{$property->name} <red>is missing doccomment in <darkRed>{$this->file}";
            }
        }
    }
}

