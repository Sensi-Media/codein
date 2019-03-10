<?php

namespace Sensi\Codein;

use Generator;
use ReflectionClass;

class Typehints extends Check
{
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
