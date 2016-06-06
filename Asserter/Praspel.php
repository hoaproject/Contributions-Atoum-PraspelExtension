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

namespace Atoum\PraspelExtension\Asserter;

use Atoum\PraspelExtension\Praspel\Model\Variable;
use Hoa\Praspel as HoaPraspel;
use mageekguy\atoum;

/**
 * Class \Atoum\PraspelExtension\Asserter.
 *
 * Praspel asserter. A simple wrapper around \Hoa\Praspel\Model\Specification.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Praspel extends atoum\asserter
{
    /**
     * Runtime Assertion Checker.
     *
     * @var \Hoa\Praspel\AssertionChecker
     */
    protected $_rac           = null;

    /**
     * Specification.
     *
     * @var \Hoa\Praspel\Model\Specification
     */
    protected $_specification = null;

    /**
     * Method name.
     *
     * @var mixed
     */
    protected $_method        = null;

    /**
     * Specific variables (isolated of the specification).
     *
     * @var array
     */
    protected $_variables     = [];



    /**
     * Alias to \Hoa\Praspel\Model\Specification::getClause().
     *
     * @return  string  $name    Clause name.
     * @return  \Hoa\Praspel\Model\Clause
     */
    public function __get($name)
    {
        return $this->_specification->getClause($name);
    }

    /**
     * Reset the asserter, i.e. create a new fresh specification.
     *
     * @param   string   $method    Callable.
     * @return  \Atoum\PraspelExtension\Asserter
     */
    public function setWith($method)
    {
        $this->_specification = new HoaPraspel\Model\Specification();
        $this->_method        = $method;

        return $this;
    }

    /**
     * Get method.
     *
     * @return  string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Compute the test verdict.
     *
     * @param   mixed    $call    Callable (first part).
     * @param   string   $able    Callable (second part).
     * @return  \Atoum\PraspelExtension\Asserter
     */
    public function verdict($call, $able = null)
    {
        $this->_rac = new HoaPraspel\AssertionChecker\Runtime(
            $this->_specification,
            xcallable($call, $able ?: $this->getMethod()),
            true
        );

        $callable   = $this->_rac->getCallable();
        $reflection = $callable->getReflection();

        if ($reflection instanceof \ReflectionMethod &&
            '__construct' !== $reflection->getName()) {
            $this->_rac->preamble(
                new HoaPraspel\Preambler\EncapsulationShunter($this->_rac)
            );
        }

        try {
            if (false === $this->_rac->evaluate()) {
                $this->fail('Verdict was false');
            }
        } catch (HoaPraspel\Exception $e) {
            $this->fail($this->raise($e));
        }

        return $this->pass();
    }

    /**
     * Pretty-print error.
     *
     * @param   \Hoa\Praspel\Exception  $exception    Exception.
     * @return  string
     */
    protected function raise(HoaPraspel\Exception $exception)
    {
        $out = null;

        if ($exception instanceof HoaPraspel\Exception\Group) {
            $out .= $exception->getMessage();

            foreach ($exception as $_exception) {
                $out .=
                    "\n" . '  • ' .
                    str_replace(
                        "\n",
                        "\n" . '    ',
                        $this->raise($_exception)
                    );
            }
        } else {
            $out = $exception->getMessage();
        }

        if ((null !== $previous = $exception->getPreviousThrow()) &&
            ($previous instanceof HoaPraspel\Exception)) {
            $out .=
                "\n" .
                '    ⬇' . "\n" .
                $this->raise($previous);
        }

        return $out;
    }

    /**
     * Get Runtime Assertion Checker.
     *
     * @return  \Hoa\Praspel\AssertionChecker
     */
    public function getRAC()
    {
        return $this->_rac;
    }

    /**
     * Get specification.
     *
     * @return  \Hoa\Praspel\Model\Specification
     */
    public function getSpecification()
    {
        return $this->_specification;
    }

    /**
     * Declare or get a variable (isolated of the specification).
     *
     * @param   string  $variable    Variable name.
     * @return  \Atoum\PraspelExtension\Praspel\Model\Variable
     */
    public function getVariable($variable)
    {
        if (!isset($this->_variables[$variable])) {
            $this->_variables[$variable] = new Variable(
                $variable,
                false,
                null,
                $this
            );
        }

        return $this->_variables[$variable];
    }
}
