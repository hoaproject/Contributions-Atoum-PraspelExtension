<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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
use Hoa\Realdom\Realdom;
 
class Manifest implements \atoum\extension {

    protected $_test;
    protected $_runner;

    public function setRunner ( \atoum\runner $runner ) {

        $this->_runner = $runner;

        return;
    }

    public function setTest ( \atoum\test $test ) {

        $this->_test = $test;

        Realdom::setDefaultSampler(new Math\Sampler\Random());
        $asserter = new Asserter($test->getAsserterGenerator());

        $this->_test
             ->getAssertionManager()
             ->setHandler('praspel', function ( ) use ( $asserter ) {

                 return $asserter;
             })
             ->setHandler('requires', function ( ) use ( $test ) {

                 return $test->praspel->requires;
             })
             ->setHandler('ensures', function ( ) use ( $test ) {

                 return $test->praspel->ensures;
             })
             ->setHandler('throwable', function ( ) use ( $test ) {

                 return $test->praspel->throwable;
             })
             ->setHandler('verdict', function ( $call, $able = null ) use ( $test ) {

                 return $test->praspel->verdict($call, $able);
             })
             ->setHandler('variable', function ( $variable ) use ( $test ) {

                 return $test->praspel->getVariable($variable);
             });

        return;
    }

    public function handleEvent ( $event, \atoum\observable $observable ) {

        return;
    }
} 
