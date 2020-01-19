<?php

use Gentry\Gentry\Wrapper;

/** Test if all type hints are correctly set (where possible) */
return function () : Generator {
    $object = Wrapper::createObject(Sensi\Codein\Typehints::class);
    /** Check type hints for parameters and return values */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Typehints.php';
        $errors = [];
        foreach ($object->check($file) as $error) {
            $errors[] = $error;
        }
        assert(count($errors) === 2);
        assert(strpos($errors[0], 'specifies no return type') !== false);
        assert(strpos($errors[1], 'has no type hint') !== false);
    };

};

