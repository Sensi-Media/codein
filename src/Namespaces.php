<?php

namespace Sensi\Codein;

use Generator;

class Namespaces
{
    /** @var string */
    protected $file;
    /** @var string */
    protected $code;

    public function check(string $file) : Generator
    {
        $this->file = $file;
        $this->code = file_get_contents(preg_replace("@/\*(.*?)\*/@ms", '', $this->file));
        if (!preg_match_all("@^use (.*?);$@ms", $this->code, $matches)) {
            return;
        }
        $namespaces = [];
        $nss = [];
        foreach ($matches[1] as $i => $match) {
            if (strpos($match, '{')) {
                $match = substr($match, strpos($match, '{') + 1);
                $nss = array_merge($nss, preg_split("@,\s*@", preg_replace("@^\s*(.*?)\s*}$@ms", '\\1', $match)));
            } else {
                $parts = explode("\\", $match);
                $nss[] = $match;
                if (count($parts) > 1) {
                    if (!isset($namespaces["{$parts[0]}\\{$parts[1]}"])) {
                        $namespaces["{$parts[0]}\\{$parts[1]}"] = 0;
                    }
                    $namespaces["{$parts[0]}\\{$parts[1]}"]++;
                }
            }
            $this->code = str_replace($matches[0][$i], '', $this->code);
        }
        foreach ($namespaces as $name => $count) {
            if ($count > 1) {
                yield new Error("Namespace $name appers $count times in {$this->file}");
            }
        }
        foreach ($nss as $i => $namespace) {
            if (strpos($namespace, ' as ')) {
                $parts = explode(' as ', $namespace);
                $namespace = end($parts);
            }
            if (strpos($namespace, '\\')) {
               $namespace = substr($namespace, strrpos($namespace, '\\') + 1);
            }
            $namespace = preg_replace("@\s*}@ms", '', trim($namespace));
            $nss[$i] = $namespace;
        }
        $nss = array_unique($nss);
        foreach ($nss as $namespace) {
            if ($this->instantiated($namespace)
                || $this->argumentTypeHint($namespace)
                || $this->traitUsed($namespace)
                || $this->returnTypeHint($namespace)
                || $this->classname($namespace)
                || $this->instanceOfCheck($namespace)
                || $this->extendsClass($namespace)
                || $this->implementsInterface($namespace)
                || $this->catchesException($namespace)
            ) {
                continue;
            }
            yield new Error("Unused: $namespace in {$this->file}");
        }
        return;
    }

    public function instantiated(string $namespace) : bool
    {
        return (bool)preg_match("@new $namespace@", $this->code);
    }

    public function argumentTypeHint(string $namespace) : bool
    {
        if (preg_match_all('@function \w*\((.*?)\)@ms', $this->code, $matches)) {
            foreach ($matches[1] as $args) {
                $ns = preg_split('@,\s*@', $args);
                foreach ($ns as $one) {
                    if (preg_match("@^$namespace@", $one)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function traitUsed(string $namespace) : bool
    {
        return (bool)preg_match("@use $namespace@", $this->code);
    }

    public function returnTypeHint(string $namespace) : bool
    {
        return (bool)preg_match("@:\?? $namespace@", $this->code);
    }

    public function classname(string $namespace) : bool
    {
        return (bool)preg_match("@$namespace(\\\\(\w|\\\\)+)?::@m", $this->code);
    }

    public function instanceOfCheck(string $namespace) : bool
    {
        return (bool)preg_match("@instanceof $namespace@", $this->code);
    }

    public function extendsClass(string $namespace) : bool
    {
        return (bool)preg_match("@extends $namespace@", $this->code);
    }

    public function implementsInterface(string $namespace) : bool
    {
        if (preg_match("@implements (.*?)$@m", $this->code, $match)) {
            $ns = preg_split('@,\s*@', $match[1]);
            foreach ($ns as $one) {
                if (preg_match("@^$namespace@", $one)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function catchesException(string $namespace) : bool
    {
        return (bool)preg_match("@} catch \($namespace @", $this->code);
    }
}

