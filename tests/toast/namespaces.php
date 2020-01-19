<?php

use Gentry\Gentry\Wrapper;

/** Test usage of namespaces */
return function () : Generator {
    $object = Wrapper::createObject(Sensi\Codein\Namespaces::class);

    /** Double namespaces are a code smell, as are unused namespaces */
    yield function () use ($object) {
        $errors = [];
        $file = dirname(__DIR__).'/files/Namespaces/Doubled.php';
        foreach ($object->check($file) as $error) {
            $errors[] = $error;
        }
        assert(strpos($errors[0], 'appears 2 times') !== false);
        assert(strpos($errors[1], 'Unused') !== false);
    };

    /** A namespace used in an instantiation is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/Instantiation.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used in an argument type hint is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/ArgumentTypehint.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used in a trait is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/TraitUsed.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used in a return type hint is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/ReturnTypehint.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used as a static classname is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/Classname.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used in an instanceof check is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/InstanceofCheck.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used by extending a class is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/ExtendsClass.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used by implementing an interface is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/ImplementsInterface.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

    /** A namespace used by catching an exception is okay */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Namespaces/CatchesException.php';
        $errors = 0;
        foreach ($object->check($file) as $error) {
            $errors++;
        }
        assert($errors === 0);
    };

};

