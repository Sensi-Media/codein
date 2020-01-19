<?php

namespace Sensi\Codein;

use ReflectionClass;
use Generator;
use Ansi;
use Throwable;

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
        if (file_exists(getcwd().'/codein.json')) {
            $config = json_decode(file_get_contents(getcwd().'/codein.json'), true);
        }
        if (isset($config, $config['constructors'], $config['constructors'][$class])) {
            foreach ($config['constructors'][$class] as $argument) {
                $args[] = eval("return $argument;");
            }
        } elseif ($constructor = $reflection->getConstructor()) {
            $cached = [];
            foreach ($constructor->getParameters() as $parameter) {
                if ($parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();
                    $cached[] = $this->toString($parameter->getDefaultValue());
                } else {
                    fwrite(STDOUT, Ansi::tagsToColors("<darkGreen>$class<green> constructor argument <darkGreen>\${$parameter->name}<green> value: <reset>"));
                    $argument = trim(fgets(STDIN));
                    $args[] = strlen($argument) ? eval("return $argument;") : null;
                    $cached[] = strlen($argument) ? $argument : 'null';
                }
            }
            if (isset($config)) {
                $config['constructors'] = $config['constructors'] ?? [];
                $config['constructors'][$class] = $cached;
                file_put_contents(getcwd().'/codein.json', json_encode($config, JSON_PRETTY_PRINT));
            }
        }
        try {
            $instance = $reflection->newInstance(...$args);
        } catch (Throwable $e) {
            yield "<red>Could not construct <darkRed>$class <red>from <darkRed>{$this->file}; specify construction arguments manually in <darkRed>codein.json";
            return;
        }
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

    /**
     * Internal helper to format random arguments as a string.
     *
     * @param mixed $arg
     * @return string
     */
    private function toString($arg) : string
    {
        if (is_null($arg)) {
            return 'null';
        }
        if (is_string($arg)) {
            return "'".addslashes($arg)."'";
        }
        if (is_array($arg)) {
            return $this->stringifyArray($arg);
        }
        if (is_bool($arg)) {
            return $arg ? 'true' : 'false';
        }
        // Prolly int or float?
        return $arg;
    }

    /**
     * Internal helper to stringify an array.
     *
     * @param array $arg
     * @return string
     */
    private function stringifyArray(array $arg) : string
    {
        $ret = '[';
        $i = 0;
        foreach ($arg as $key => $value) {
            if ($i) {
                $ret .= ', ';
            }
            $ret .= '"'.$key.'" => '.$this->toString($value);
            $i++;
        }
        $ret .= ']';
        return $ret;
    }
}

