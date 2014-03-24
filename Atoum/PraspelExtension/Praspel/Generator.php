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

namespace Atoum\PraspelExtension\Praspel;

use Hoa\Praspel as HoaPraspel;
use Hoa\Core;

/**
 * Class \Atoum\PraspelExtension\Praspel\Generator.
 *
 * Generate tests based on a Praspel-annotated class.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Generator  {

    /**
     * Namespace of the test.
     *
     * @var \Atoum\PraspelExtension\Praspel\Generator string
     */
    protected $_testNamespace   = null;

    /**
     * Compiler.
     *
     * @var \Atoum\PraspelExtension\Praspel\Visitor\Compiler object
     */
    protected $_compiler        = null;

    /**
     * Praspel compiler.
     *
     * @var \Hoa\Praspel\Visitor\Compiler object
     */
    protected $_praspelCompiler = null;



    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        $this->_compiler        = new Visitor\Compiler();
        $this->_praspelCompiler = new HoaPraspel\Visitor\Compiler();

        return;
    }

    /**
     * Generate tests based on a Praspel-annotated class.
     *
     * @access  public
     * @param   \ReflectionClass  $class     Annotated class.
     * @return  string
     */
    public function generate ( $class ) {

        if($class instanceof \ReflectionClass)
            $class = new Reflection\RClass($class);

        if(!($class instanceof Reflection\RClass))
            throw new Exception(
                'Generate works only with reflection instances.', 0);

        $registry  = HoaPraspel\Praspel::getRegistry();
        $className = '\\' . $class->getName();
        $out       = '<?php' . "\n\n" .
                     'namespace ' . $this->getTestNamespace() .
                     (true === $class->inNamespace()
                         ? '\\' . $class->getNamespaceName()
                         : '') . ';' . "\n\n" .
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

        foreach($class->getProperties() as $property) {

            $propertyName  = $property->getName();
            $contract      = \Hoa\Praspel::extractFromComment($property->getDocComment());
            $id            = $className . '::$' . $propertyName;

            if(empty($contract)) {

                $out .= "\n" . $__ .
                        '// Property ' . $propertyName . ' is not specified.';

                continue;
            }

            try {

                $specification = HoaPraspel\Praspel::interprete($contract);
            }
            catch ( Core\Exception $e ) {

                throw new Exception(
                    'The property %s has an ' .
                    'error in the following contract:' . "\n\n" . '%s' . "\n",
                    1,
                    array(
                        $id,
                        '    ' . str_replace("\n", "\n" . '    ', $contract)
                    ),
                    $e);
            }

            if(false === $specification->clauseExists('invariant')) {

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
                    $propertyName. '\'] = ' .
                    str_replace(
                        array("\n\n", "\n"),
                        array("\n",   "\n" . $__),
                        $this->_praspelCompiler->visit($specification)
                    ) .
                    '$praspel->bindToClass(\'' . $className . '\');' . "\n" .
                    $__ . '$specification->addClause($praspel->getClause(\'invariant\'));' . "\n";
        }

        $out .= "\n" .
                $__ . 'return $out;' . "\n" .
                $_ . '}' . "\n";

        foreach($class->getMethods() as $method) {

            $methodName = $method->getName();
            $contract   = HoaPraspel\Praspel::extractFromComment(
                $method->getDocComment()
            );

            if(empty($contract)) {

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

                $specification = HoaPraspel\Praspel::interprete($contract, $className);
            }
            catch ( Core\Exception $e ) {

                throw new Exception(
                    'The method %s (in %s) has an ' .
                    'error in the following contract:' . "\n\n" . '%s' . "\n",
                    1,
                    array(
                        $className . '::' . $methodName,
                        $method->getFileName() . '#' . $method->getStartLine(),
                        '    ' . str_replace("\n", "\n" . '    ', $contract)
                    ),
                    $e);
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

            foreach($coverage as $path) {

                $_out = "\n" .
                        $_ . 'public function test ' . $methodName .
                        ' n°' . $i++ . ' ( ) {' . "\n\n" .
                        $__ . '$this';

                $j = 0;

                foreach($path['pre'] as $clause) {

                    $k    = $j++;
                    $_out .= str_replace(
                        "\n",
                        "\n" . $___,
                        $this->_compiler->visit($clause, $k)
                    );
                }

                foreach($path['post'] as $clause) {

                    $k    = $j++;
                    $_out .= str_replace(
                        "\n",
                        "\n" . $___,
                        $this->_compiler->visit($clause, $k)
                    );
                }

                if(0 === $j) {

                    --$i;

                    continue;
                }

                $out .= $_out . "\n" .
                        $___ . '->then' . "\n" .
                        $____ . '->praspel->verdict(\'' . $className . '\'); '. "\n\n" .
                        $__ . 'return;' . "\n" .
                        $_ . '}' . "\n";
            }
        }

        $out .= '}' . "\n";

        return $out;
    }

    /**
     * Set the test namespace.
     *
     * @access  public
     * @param   string  $testNamespace     Test namespace.
     * @return  string
     */
    public function setTestNamespace ( $testNamespace ) {

        $old                  = $this->_testNamespace;
        $this->_testNamespace = trim($testNamespace, '\\');

        return $old;
    }

    /**
     * Get the test namespace.
     *
     * @access  public
     * @return  string
     */
    public function getTestNamespace ( ) {

        return $this->_testNamespace;
    }
}
