<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Atoum\PraspelExtension;

use Hoa\Math;
use Hoa\Realdom;
use mageekguy\atoum;

/**
 * Class \Atoum\PraspelExtension\Manifest.
 *
 * Bootstrap of the extension.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Manifest implements atoum\extension
{
    /**
     * Test instance.
     *
     * @var \atoum\runner
     */
    protected $_test   = null;

    /**
     * Runner instance.
     *
     * @var \atoum\runner
     */
    protected $_runner = null;



    /**
     * Hook for the runner.
     *
     * @param   \atoum\runner  $runner    Runner instance.
     * @return  void
     */
    public function setRunner(atoum\runner $runner)
    {
        $this->_runner = $runner;

        return;
    }

    /**
     * Hook for the test.
     *
     * @param   \atoum\test  $test    Test instance.
     * @return  void
     */
    public function setTest(atoum\test $test)
    {
        $this->_test = $test;

        Realdom\Realdom::setDefaultSampler(new Math\Sampler\Random());
        $asserterGenerator = $test->getAsserterGenerator();
        $realdom           = new Asserter\Realdom($asserterGenerator);
        $praspel           = new Asserter\Praspel($asserterGenerator);

        $this->_test
             ->getAssertionManager()
             ->setHandler('realdom', function () use ($realdom) {
                 return $realdom;
             })
             ->setHandler('sample', function (Realdom\Disjunction $realdom) use ($test) {
                 return $test->realdom->sample($realdom);
             })
             ->setHandler('sampleMany', function (Realdom\Disjunction $realdom, $n = 7) use ($test) {
                 return $test->realdom->sampleMany($realdom, $n);
             })
             ->setHandler('praspel', function () use ($praspel) {
                 return $praspel;
             });

        return;
    }

    /**
     * Hook for event handler.
     *
     * @param   string             $event         Event name.
     * @param   \atoum\observable  $observable    Observable.
     * @return  void
     */
    public function handleEvent($event, atoum\observable $observable)
    {
        return;
    }
}
