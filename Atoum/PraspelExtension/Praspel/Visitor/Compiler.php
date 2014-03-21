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

namespace Atoum\PraspelExtension\Praspel\Visitor {

use Hoa\Core;
use Hoa\Praspel;
use Hoa\Realdom;
use Hoa\Visitor;

/**
 * Class \Atoum\PraspelExtension\Praspel\Visitor\Compiler.
 *
 * Compile the model to atoum code.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Compiler extends Praspel\Visitor\Compiler {

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    public function visit ( Visitor\Element $element,
                            &$handle = null, $eldnah = null ) {

        $out   = null;
        $first = 0 === $handle;

        // Hoa\Praspel.

        if($element instanceof Praspel\Model\Declaration) {

            if(true === $first) {

                $variable = "\n" . '->if(';
                $first    = false;
            }
            else
                $variable = "\n" . '->and(';

            foreach($element as $var)
                $out .= $var->accept($this, $handle, $eldnah);

            foreach($element->getPredicates() as $predicate)
                $out .= $variable . '->predicate(\'' . $predicate . '\')';
        }
        elseif($element instanceof Praspel\Model\Variable) {

            $_variable = $eldnah ?: '$this->praspel->' . $element->getClause()->getName();

            if(true === $first) {

                $variable = "\n" . '->if(';
                $first    = false;
            }
            else
                $variable = "\n" . '->and(';

            $variable .= $_variable;
            $start     = $variable . '[\'' . $element->getName() . '\']';
            $out      .= $start;

            if(null === $alias = $element->getAlias())
                $out .= '->in = ' .
                        $element->getDomains()->accept($this, $handle, $eldnah) .
                        ')';
            else
                $out .= '->domainof(\'' . $alias . '\')';

            $constraints = $element->getConstraints();

            if(isset($constraints['is']))
                $out .= $start . '->is(\'' .
                        implode('\', \'', $constraints['is']) . '\')';

            if(isset($constraints['contains']))
                foreach($constraints['contains'] as $contains)
                    $out .= $start . '->contains(' . $contains . ')';

            if(isset($constraints['key']))
                foreach($constraints['key'] as $pairs)
                    $out .= $start . '->key(' . $pairs[0] . ')->in = ' .
                            $pairs[1];
        }
        elseif($element instanceof Praspel\Model\Throwable) {

            $_variable = '$this->praspel->' . $element->getName();

            if(true === $first) {

                $variable = "\n" . '->if(';
                $first    = false;
            }
            else
                $variable = "\n" . '->and(';

            foreach($element as $identifier) {

                $exception = $element[$identifier];
                $start     = $variable . $_variable .
                             '[\'' . $identifier . '\']';
                $out      .= $start . ' = \'' . $exception->getInstanceName() .
                             '\')';

                if(false === $element->isDisjointed()) {

                    if(null !== $with = $element->getWith()) {

                        $temp = $element->getName() . '_' . $identifier . '_with';
                        $out .= $variable .'$' . $temp . ' = ' .
                                $_variable . '->newWith())';

                        foreach($with as $var)
                            $out .= $var->accept($this, $handle, '$' . $temp);

                        foreach($with->getPredicates() as $predicate)
                            $out .= $variable . '$' . $temp . '->predicate(\'' .
                                    $predicate . '\'))';

                        $out .= $start . '->setWith($' . $temp . '))';
                    }
                }
                else
                    $out .= $start . '->disjunctionWith(\'' .
                            $exception->getDisjunction() . '\'))';
            }
        }
        else
            $out = parent::visit($element, $handle, $eldnah);

        return $out;
    }
}

}
