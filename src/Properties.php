<?php

namespace Sensi\Codein;

use ReflectionClass;
use Generator;
use Ansi;

/**
 * Check if all properties on the class are correctly defined and nothing is
 * "set on the fly".
 */
class Properties extends Check
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
        if ($reflection->isAbstract()) {
            return;
        }
        $args = [];
        if ($constructor = $reflection->getConstructor()) {
            foreach ($constructor->getParameters() as $parameter) {
                fwrite(STDOUT, Ansi::tagsToColors("<darkGreen>$class<green> constructor argument <darkGreen>\${$parameter->name}<green> value: <reset>"));
                $argument = trim(fgets(STDIN));
                $args[] = strlen($argument) ? $argument : null;
            }
        }
        $instance = $reflection->newInstance(...$args);
        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            $properties[$property->name] = true;
        }
        foreach ($instance as $property => $value) {
            if (!isset($properties[$property])) {
                yield "<red>Property <darkRed>$class->$property <red>is added on the fly in <darkRed>{$this->file}";
            }
        }
    }
}

