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

namespace Atoum\PraspelExtension\Praspel\Reflection {

/**
 * Class \Atoum\PraspelExtension\Praspel\Reflection\RMethod.
 *
 *
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class RMethod
{
    protected $_reflection = null;
    protected $_name       = null;
    protected $_docComment = null;
    protected $_filename   = null;
    protected $_startLine  = null;

    public function __construct(\ReflectionMethod $method)
    {
        $this->setReflection($method);

        return;
    }

    protected function setReflection(\ReflectionMethod $reflection)
    {
        $old               = $this->_reflection;
        $this->_reflection = $reflection;
        $this->_name       = $reflection->getName();
        $this->_docComment = $reflection->getDocComment();
        $this->_filename   = $reflection->getFileName();
        $this->_startLine  = $reflection->getStartLine();

        return $old;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getDocComment()
    {
        return $this->_docComment;
    }

    public function getFileName()
    {
        return $this->_filename;
    }

    public function getStartLine()
    {
        return $this->_startLine;
    }

    public function __sleep()
    {
        $out = get_object_vars($this);
        unset($out['_reflection']);

        return array_keys($out);
    }
}

}
