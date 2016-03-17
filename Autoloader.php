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

use mageekguy\atoum;

$vendorDirectory = __DIR__ . '/vendor';

if (is_dir($vendorDirectory) === false) {
    $vendorDirectory = __DIR__ . '/../..';
}

atoum\autoloader::get()
    ->addNamespaceAlias('atoum\praspel', __NAMESPACE__)
    ->addNamespaceAlias('mageekguy\atoum\praspel', __NAMESPACE__)
    ->addClassAlias('atoum\praspel\extension', __NAMESPACE__ . '\\Manifest')
    ->addClassAlias('mageekguy\atoum\praspel\extension', __NAMESPACE__ . '\\Manifest')
    ->addDirectory(__NAMESPACE__, __DIR__)
    ->addDirectory('Hoa\Compiler', $vendorDirectory . '/hoa/compiler')
    ->addDirectory('Hoa\Console', $vendorDirectory . '/hoa/console')
    ->addDirectory('Hoa\Dispatcher', $vendorDirectory . '/hoa/dispatcher')
    ->addDirectory('Hoa\Exception', $vendorDirectory . '/hoa/exception')
    ->addDirectory('Hoa\File', $vendorDirectory . '/hoa/file')
    ->addDirectory('Hoa\Iterator', $vendorDirectory . '/hoa/iterator')
    ->addDirectory('Hoa\Math', $vendorDirectory . '/hoa/math')
    ->addDirectory('Hoa\Praspel', $vendorDirectory . '/hoa/praspel')
    ->addDirectory('Hoa\Realdom', $vendorDirectory . '/hoa/realdom')
    ->addDirectory('Hoa\Regex', $vendorDirectory . '/hoa/regex')
    ->addDirectory('Hoa\Router', $vendorDirectory . '/hoa/router')
    ->addDirectory('Hoa\Stream', $vendorDirectory . '/hoa/stream')
    ->addDirectory('Hoa\String', $vendorDirectory . '/hoa/ustring')
    ->addDirectory('Hoa\Visitor', $vendorDirectory . '/hoa/visitor');
