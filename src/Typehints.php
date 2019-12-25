<?php

namespace Sensi\Codein;

use Generator;
use ReflectionClass;

/**
 * Check if all methods typehint their parameters and return types.
 */
class Typehints extends Check
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
        foreach ($reflection->getMethods() as $method) {
            if (in_array($method->name, ['__construct', '__destruct', '__get'])) {
                continue;
            }
            if ($method->getDeclaringClass()->name != $class) {
                continue;
            }
            if ($method->getFileName() != $reflection->getFileName()) {
                continue;
            }
            // TODO: if this class extends another class, specifying these
            // might be illegal according to PHP. Check that!
            if (!$method->getReturnType()) {
                yield "<red>Method <darkRed>{$method->name} <red>specifies no return type in <darkRed>$file";
            }
            foreach ($method->getParameters() as $parameter) {
                if (!$parameter->hasType()) {
                    $name = $parameter->getName();
                    yield "<red>Parameter <darkRed>$name <red> in method <darkRed>{$method->name} <red> has no type hint in <darkRed>$file";
                }
            }
        }
    }
}

