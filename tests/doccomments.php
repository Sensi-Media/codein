<?php

use Gentry\Gentry\Wrapper;

class Foo
{
    public function bar()
    {
    }

    /**
     * This method has a doccomment, so it isn't flagged.
     */
    public function _foo()
    {
    }
}

/** Testsuite for Sensi\Codein\Doccomments */
return function () : Generator {
    $object = Wrapper::createObject(Sensi\Codein\Doccomments::class);

    /** Missing doccomments on both a class and a method are flagged as errors */
    yield function () use ($object) {
        $code = <<<EOT
<?php

class Foo
{
}
EOT;
        $i = 0;
        foreach ($object->check($code) as $error) {
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

