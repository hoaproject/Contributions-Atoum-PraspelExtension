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

use Hoa\Iterator as HoaIterator;
use Hoa\Realdom as HoaRealdom;
use mageekguy\atoum;

/**
 * Class \Atoum\PraspelExtension\Asserter\Realdom.
 *
 * Data generator. Wrapper around Hoa\Realdom.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Realdom extends atoum\asserter
{
    /**
     * Create a new disjunction of realdoms.
     *
     * @return  \Hoa\Realdom\Disjunction
     */
    public function newDisjunction()
    {
        return new HoaRealdom\Disjunction();
    }

    /**
     * Create a new disjunction and call a realdom on it.
     *
     * @param   string  $name         Name of the realdom.
     * @param   array   $arguments    Arguments of the realdom.
     * @return  \Hoa\Realdom\Disjunction
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(
            [$this->newDisjunction(), $name],
            $arguments
        );
    }

    /**
     * Sample a value from a disjunction of realdoms.
     *
     * @param   \Hoa\Realdom\Disjunction  $realdoms    A disjunction of realdoms.
     * @return  mixed
     */
    public function sample(HoaRealdom\Disjunction $realdoms)
    {
        return $realdoms->sample();
    }

    /**
     * Sample several values from a disjunction of realdoms.
     *
     * @param   \Hoa\Realdom\Disjunction  $realdoms    A disjunction of realdoms.
     * @param   int                       $n           Number of values.
     * @return  mixed
     */
    public function sampleMany(HoaRealdom\Disjunction $realdoms, $n = 7)
    {
        return new HoaIterator\Limit(
            new HoaIterator\CallbackGenerator(
                function () use ($realdoms) {
                    return $realdoms->sample();
                }
            ),
            0,
            $n
        );
    }
}
