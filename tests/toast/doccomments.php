<?php

use Gentry\Gentry\Wrapper;

/** Testsuite for Sensi\Codein\Doccomments */
return function () : Generator {
    $object = Wrapper::createObject(Sensi\Codein\Doccomments::class);

    /** Missing doccomments on both a class and a method are flagged as errors */
    yield function () use ($object) {
        $file = dirname(__DIR__).'/files/Doccomment.php';
        $i = 0;
        foreach ($object->check($file) as $error) {
            if (!$i) {
                assert(strpos($error, 'Class') !== false);
            } else {
                assert(strpos($error, 'Method') !== false);
            }
            ++$i;
        }
        assert($i === 2);
    };

};

