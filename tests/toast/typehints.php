<?php

use Gentry\Gentry\Wrapper;

/** Testsuite for Sensi\Codein\Typehints */
return function () : Generator {
    $object = Wrapper::createObject(Sensi\Codein\Typehints::class);
    /** check yields $result instanceof Generator */
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

