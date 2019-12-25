<?php

namespace Sensi\Codein;

use ReflectionClass;
use Generator;

class Doccomments extends Check
{
    public function check(string $code) : Generator
    {
        if (!($class = $this->extractClass($code))) {
            return;
        }
        $reflection = new ReflectionClass($class);
        if (!$reflection->getDocComment()) {
            yield "<red>Class <darkRed>$class <red>is missing doccomment";
        }
        foreach ($reflection->getMethods() as $method) {
            if ($method->getDeclaringClass()->name != $class) {
                var_dump($method->getDeclaringClass()->name, $class);
                var_dump('class');
                continue;
            }
            if ($method->getFileName() != $reflection->getFileName()) {
                var_dump('method');
                continue;
            }
            if (!$method->getDocComment()) {
                yield "<red>Method <darkRed>$class::{$method->name} <red>is missing doccomment";
            }
        }
    }
}

