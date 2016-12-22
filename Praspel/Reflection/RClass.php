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
 * Class \Atoum\PraspelExtension\Praspel\Reflection\RClass.
 *
 *
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class RClass
{
    protected $_reflection     = null;
    protected $_name           = null;
    protected $_shortName      = null;
    protected $_filename       = null;
    protected $_inNamespace    = null;
    protected $_namespaceName  = null;
    protected $_properties     = [];
    protected $_methods        = [];
    protected $_isInstantiable = false;

    public function __construct($class)
    {
        if (!($class instanceof \ReflectionClass)) {
            $class = new \ReflectionClass($class);
        }

        $this->setReflection($class);

        return;
    }

    protected function setReflection(\ReflectionClass $reflection)
    {
        $old                   = $this->_reflection;
        $this->_reflection     = $reflection;
        $this->_name           = $reflection->getName();
        $this->_shortName      = $reflection->getShortName();
        $this->_filename       = $reflection->getFileName();
        $this->_inNamespace    = $reflection->inNamespace();
        $this->_namespaceName  = $reflection->getNamespaceName();
        $this->setProperties($reflection->getProperties());
        $this->setMethods($reflection->getMethods());
        $this->_isInstantiable = $reflection->isInstantiable();

        return $old;
    }

    protected function setProperties(array $properties)
    {
        foreach ($properties as $property) {
            $this->_properties[$property->getName()] = new RProperty($property);
        }

        return;
    }

    protected function setMethods(array $methods)
    {
        foreach ($methods as $method) {
            $this->_methods[$method->getName()] = new RMethod($method);
        }

        return;
    }

    public function getReflection()
    {
        return $this->_reflection;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getShortName()
    {
        return $this->_shortName;
    }

    public function getFileName()
    {
        return $this->_filename;
    }

    public function inNamespace()
    {
        return $this->_inNamespace;
    }

    public function getNamespaceName()
    {
        return $this->_namespaceName;
    }

    public function hasProperty($name)
    {
        return isset($this->_properties[$name]);
    }

    public function getProperty($name)
    {
        if (false === $this->hasProperty($name)) {
            return false;
        }

        return $this->_properties[$name];
    }

    public function getProperties()
    {
        return $this->_properties;
    }

    public function hasMethod($name)
    {
        return isset($this->_methods[$name]);
    }

    public function getMethod($name)
    {
        if (false === $this->hasMethod($name)) {
            return null;
        }

        return $this->_methods[$name];
    }

    public function getMethods()
    {
        return $this->_methods;
    }

    public function isInstantiable()
    {
        return $this->_isInstantiable;
    }

    public function __sleep()
    {
        $out = get_object_vars($this);
        unset($out['_reflection']);

        return array_keys($out);
    }
}

}
