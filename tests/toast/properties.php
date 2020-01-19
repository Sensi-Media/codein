<?php

use Gentry\Gentry\Wrapper;

/** Test on-the-fly properties */
return function () : Generator {
    $object = Wrapper::createObject(Sensi\Codein\Properties::class);

    /** Missing doccomments on a class, method AND property are flagged as errors */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Property.php';
        $i = 0;
        foreach ($object->check($file) as $error) {
            assert(strpos($error, 'Property') !== false);
            ++$i;
        }
        assert($i === 1);
    };

};

