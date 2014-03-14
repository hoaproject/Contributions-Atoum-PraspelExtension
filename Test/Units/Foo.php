<?php

namespace Test\Units;

class stdClass extends \mageekguy\atoum\test {

    public function testHoa ( ) {

        $this->integer(42)->isGreaterThan(10);
    }

    public function testProject ( ) {

        $this->variable('i')->in = realdom()->regex('/foo[a-f]+/');

        $this->string($this->variable('i')->sample())
             ->contains('foo');
    }
}
