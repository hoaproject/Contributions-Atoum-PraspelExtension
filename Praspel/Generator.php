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

namespace Atoum\PraspelExtension\Praspel;

use Hoa\Exception;
use Hoa\Praspel as HoaPraspel;

/**
 * Class \Atoum\PraspelExtension\Praspel\Generator.
 *
 * Generate tests based on a Praspel-annotated class.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Generator
{
    /**
     * Test namespacer, i.e. a function that computes the namespace.
     *
     * @var \Closure
     */
    protected $_testNamespacer = null;

    /**
     * Compiler.
     *
     * @var \Hoa\Praspel\Visitor\Compiler
     */
    protected $_compiler       = null;



    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_compiler = new HoaPraspel\Visitor\Compiler();

        return;
    }

    /**
     * Generate tests based on a Praspel-annotated class.
     *
     * @param   \ReflectionClass  $class     Annotated class.
     * @return  string
     */
    public function generate($class)
    {
        if ($class instanceof \ReflectionClass) {
            $class = new Reflection\RClass($class);
        }

        if (!($class instanceof Reflection\RClass)) {
            throw new Exception(
                'Generate works only with reflection instances.',
                0
            );
        }

        $namespacer = $this->getTestNamespacer();

        if (null === $namespacer) {
            $namespacer = function ($namespace) {
                return $namespace;
            };
        }

        $registry  = HoaPraspel\Praspel::getRegistry();
        $className = '\\' . $class->getName();
        $out       = '<?php' . "\n\n" .
                     'namespace ' .
                     (true === $class->inNamespace()
                         ? $namespacer($class->getNamespaceName())
                         : $namespacer('')
                     ) . ';' . "\n\n" .
                     'class ' . $class->getShortName() .
                     ' extends \Atoum\PraspelExtension\Test {' . "\n";
        $_         = '    ';
        $__        = $_ . $_;
        $___       = $_ . $_ . $_;
        $____      = $_ . $_ . $_ . $_;

        $out .= "\n" . $_ . '/**' . "\n" .
                $_ . ' * 1. Bind current specification to the tested class.' . "\n" .
                $_ . ' * 2. Declare invariants.' . "\n" .
                $_ . ' */' . "\n" .
                $_ . 'public function beforeTestMethod ( $method ) {' . "\n\n" .
                $__ . '$out = parent::beforeTestMethod($method);' . "\n" .
                $__ . '$specification = $this->praspel->getSpecification();' . "\n" .
                $__ . '$specification->bindToClass(\'' . $className . '\');' . "\n" .
                $__ . '$registry = \Hoa\Praspel::getRegistry();' . "\n";

        foreach ($class->getProperties() as $property) {
            $propertyName  = $property->getName();
            $contract      = \Hoa\Praspel::extractFromComment($property->getDocComment());
            $id            = $className . '::$' . $propertyName;

            if (empty($contract)) {
                $out .= "\n" . $__ .
                        '// Property ' . $propertyName . ' is not specified.';

                continue;
            }

            try {
                $specification = HoaPraspel\Praspel::interpret($contract);
            } catch (Exception $e) {
                throw new Exception(
                    'The property %s has an ' .
                    'error in the following contract:' . "\n\n" . '%s' . "\n",
                    1,
                    [
                        $id,
                        '    ' . str_replace("\n", "\n" . '    ', $contract)
                    ],
                    $e
                );
            }

            if (false === $specification->clauseExists('invariant')) {
                $out .= "\n" . $__ .
                        '// Property ' . $propertyName . ' is not specificied' .
                        ' (no @invariant).';

                continue;
            }

            $registry[ltrim($id, '\\')] = $specification;

            $out .= "\n" .
                    $__ . '/**' . "\n" .
                    $__ . ' * ' . str_replace(
                        "\n",
                        "\n" . $__ . ' * ',
                        $contract
                    ) . "\n" .
                    $__ . ' */' .
                    "\n" . $__ .
                    '$registry[\'' . ltrim($className, '\\') . '::$' .
                    $propertyName . '\'] = ' .
                    str_replace(
                        ["\n\n", "\n"],
                        ["\n",   "\n" . $__],
                        $this->_compiler->visit($specification)
                    ) .
                    '$praspel->bindToClass(\'' . $className . '\');' . "\n";
        }

        $out .= "\n" .
                $__ . 'return $out;' . "\n" .
                $_ . '}' . "\n";

        foreach ($class->getMethods() as $method) {
            $methodName = $method->getName();
            $contract   = HoaPraspel\Praspel::extractFromComment(
                $method->getDocComment()
            );

            if (empty($contract)) {
                $out .= "\n" . $_ . '/**' . "\n" .
                        $_ . ' * Method: ' .
                        ($full = $className . '::' . $methodName) . '.' . "\n" .
                        $_ . ' * Location: ' . $method->getFileName() .
                        '#' . $method->getStartLine() . '.' . "\n" .
                        $_ . ' * Hash: ' . md5($full) . '.' . "\n" .
                        $_ . ' */' . "\n\n" .
                        $_ . 'public function test ' . $methodName .
                        ' ¦ untested ( ) { }' . "\n";

                continue;
            }

            try {
                $specification = HoaPraspel\Praspel::interpret($contract, $className);
            } catch (Exception $e) {
                throw new Exception(
                    'The method %s (in %s) has an ' .
                    'error in the following contract:' . "\n\n" . '%s' . "\n",
                    2,
                    [
                        $className . '::' . $methodName,
                        $method->getFileName() . '#' . $method->getStartLine(),
                        '    ' . str_replace("\n", "\n" . '    ', $contract)
                    ],
                    $e
                );
            }

            $coverage = new HoaPraspel\Iterator\Coverage($specification);
            $coverage->setCriteria(
                $coverage::CRITERIA_NORMAL
              | $coverage::CRITERIA_EXCEPTIONAL
            );

            $out .= "\n" . $_ . '/**' . "\n" .
                    $_ . ' * Method: ' .
                    ($full = $className . '::' . $methodName) . '.' . "\n" .
                    $_ . ' * Location: ' . $method->getFileName() .
                    '#' . $method->getStartLine() . '.' . "\n" .
                    $_ . ' * Hash: ' . md5($full) . '.' . "\n" .
                    $_ . ' * Specification:' . "\n" .
                    $_ . ' * ' . "\n" .
                    $_ . ' *     ' .
                    str_replace("\n", "\n" . $_ . ' *     ', $contract) . "\n" .
                    $_ . ' */' . "\n";

            $i = 1;

            foreach ($coverage as $path) {
                $_out = "\n" .
                        $_ . 'public function test ' . $methodName .
                        ' n°' . $i++ . ' ( ) {' . "\n\n" .
                        $__ . '$praspel = $this->praspel->getSpecification();' . "\n";

                $j = 0;

                $fragments = ['praspel' => true];

                foreach ($path['pre'] as $clause) {
                    while ((null !== $parent = $clause->getParent())
                          && ($id = $parent->getId())
                          && (!isset($fragments[$id]))) {
                        $_out .= $__ .
                                 '$' . $id . ' = $' . $parent->getParent()->getId() .
                                 '->getClause(\'behavior\')' .
                                 '->get(\'' . $parent->getIdentifier() . '\');' . "\n";

                        $fragments[$id] = true;
                    }

                    $k    = $j++;
                    $_out .= str_replace(
                        "\n",
                        "\n" . $__,
                        $this->_compiler->visit($clause)
                    );
                }

                foreach ($path['post'] as $clause) {
                    while ((null !== $parent = $clause->getParent())
                          && ($id = $parent->getId())
                          && (!isset($fragments[$id]))) {
                        $_out .= $__ .
                                 '$' . $id . ' = $' .
                                 $parent->getParent()->getId() .
                                 '->getClause(\'behavior\')' .
                                 '->get(\'' . $parent->getIdentifier() . '\');' . "\n";

                        $fragments[$id] = true;
                    }

                    $k    = $j++;
                    $_out .= str_replace(
                        "\n",
                        "\n" . $__,
                        $this->_compiler->visit($clause)
                    );
                }

                if (0 === $j) {
                    --$i;

                    continue;
                }

                $out .= $_out . "\n" .
                        $__ . '$this->praspel->verdict(\'' . $className . '\'); ' . "\n\n" .
                        $__ . 'return;' . "\n" .
                        $_ . '}' . "\n";
            }
        }

        $out .= '}' . "\n";

        return $out;
    }

    /**
     * Set the test namespacer.
     *
     * @param   \Closure  $namespacer     Namespacer.
     * @return  \Closure
     */
    public function setTestNamespacer(\Closure $namespacer)
    {
        $old                   = $this->_testNamespacer;
        $this->_testNamespacer = $namespacer;

        return $old;
    }

    /**
     * Get the test namespacer.
     *
     * @return  \Closure
     */
    public function getTestNamespacer()
    {
        return $this->_testNamespacer;
    }
}
