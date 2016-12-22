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

namespace Atoum\PraspelExtension\Praspel\Model;

use Atoum\PraspelExtension\Asserter;
use Hoa\Praspel;

/**
 * Class \Atoum\PraspelExtension\Praspel\Model\Variable.
 *
 * Extend Praspel variables.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Variable extends Praspel\Model\Variable
{
    /**
     * Asserter.
     *
     * @var \Atoum\PraspelExtension\Asserter
     */
    protected $_asserter = null;



    /**
     * Build a variable.
     *
     * @param   string                              $name        Name.
     * @param   bool                                $local       Local.
     * @param   \Hoa\Praspel\Model\Clause           $clause      Clause.
     * @param   \Atoum\PraspelExtension\Asserter    $asserter    Asserter.
     * @throws  \Hoa\Praspel\Exception\Model
     */
    public function __construct(
        $name,
        $local,
        Clause $clause = null,
        Asserter $asserter
    ) {
        parent::__construct($name, $local, $clause);
        $this->_asserter = $asserter;

        return;
    }

    /**
     * Get the asserter.
     *
     * @return  \Atoum\PraspelExtension\Asserter
     */
    public function getAsserter()
    {
        return $this->_asserter;
    }

    /*
     * Call the predicate() method on realistic domains.
     *
     * @access  public
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    public function predicate($q = null)
    {
        $asserter  = $this->getAsserter();
        $predicate = parent::predicate($q);

        $asserter->boolean($predicate)
                 ->isTrue();

        return $asserter;
    }
}
