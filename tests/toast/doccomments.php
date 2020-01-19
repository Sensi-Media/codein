<?php

use Gentry\Gentry\Wrapper;

/** Test existence of doccomments */
return function () : Generator {
    $object = Wrapper::createObject(Sensi\Codein\Doccomments::class);

    /** Missing doccomments on a class, method AND property are flagged as errors */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Doccomment.php';
        $i = 0;
        foreach ($object->check($file) as $error) {
            if (!$i) {
                assert(strpos($error, 'Class') !== false);
            } elseif ($i == 1) {
                assert(strpos($error, 'Method') !== false);
            } else {
                assert(strpos($error, 'Property') !== false);
            }
            ++$i;
        }
        assert($i === 3);
    };

};

