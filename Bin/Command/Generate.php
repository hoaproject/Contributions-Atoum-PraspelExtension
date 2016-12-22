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

namespace Atoum\PraspelExtension\Bin\Command;

use Atoum\PraspelExtension as Extension;
use Hoa\Consistency;
use Hoa\Console;
use Hoa\Event;

/**
 * Class Atoum\PraspelExtension\Bin\Command\Generate.
 *
 * Compile Praspel test suite into atoum test suite.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Generate extends Console\Dispatcher\Kit
{
    /**
     * Options description.
     *
     * @var array
     */
    protected $options = [
        ['bootstrap',      Console\GetOption::REQUIRED_ARGUMENT, 'b'],
        ['class',          Console\GetOption::REQUIRED_ARGUMENT, 'c'],
        ['test-namespace', Console\GetOption::REQUIRED_ARGUMENT, 'n'],
        ['test-root',      Console\GetOption::REQUIRED_ARGUMENT, 'r'],
        ['help',           Console\GetOption::NO_ARGUMENT,       'h'],
        ['help',           Console\GetOption::NO_ARGUMENT,       '?']
    ];



    /**
     * The entry method.
     *
     * @return  int
     */
    public function main()
    {
        $bootstrap     = null;
        $classes       = [];
        $testNamespace = 'tests\praspel';
        $testRoot      = null;

        while (false !== $c = $this->getOption($v)) {
            switch ($c) {
                case 'b':
                    $bootstrap = $v;

                    break;

                case 'c':
                    $classes = array_merge(
                        $classes,
                        $this->parser->parseSpecialValue($v)
                    );

                    break;

                case 'n':
                    $testNamespace = $v;

                    break;

                case 'r':
                    $testRoot = $v;

                    break;

                case '__ambiguous':
                    $this->resolveOptionAmbiguity($v);

                    break;

                case 'h':
                case '?':
                default:
                    return $this->usage();
            }
        }

        if (null === $bootstrap) {
            $bootstrapDirectory = getcwd();
            $bootstrapFile      = '.bootstrap.atoum.php';

            while ((false === file_exists($bootstrapDirectory . DS . $bootstrapFile)) &&
                   ($bootstrapDirectory !== $handle = dirname($bootstrapDirectory))) {
                $bootstrapDirectory = $handle;
            }

            $bootstrap = $bootstrapDirectory . DS . $bootstrapFile;

            if (false === file_exists($bootstrap)) {
                throw new Extension\Exception(
                    'Bootstrap file is not found.',
                    0
                );
            }
        } elseif (false === file_exists($bootstrap)) {
            throw new Extension\Exception(
                'Bootstrap file %s does not exist.',
                1,
                $bootstrap
            );
        }

        $generator = new Extension\Praspel\Generator();
        $generator->setTestNamespacer(function ($namespace) use ($testNamespace) {
            return $testNamespace . '\\' . $namespace;
        });
        $phpBinary = Consistency::getPHPBinary() ?: Console\Processus::locate('php');

        if (null === $phpBinary) {
            throw new Extension\Exception(
                'PHP binary is not found…',
                2
            );
        }

        $envVariable   = '__ATOUM_PRASPEL_EXTENSION_' . md5(Consistency::uuid());
        $reflection    = null;
        $buffer        = null;
        $reflectionner = new Console\Processus($phpBinary);
        $reflectionner->on('input', function (Event\Bucket $bucket) use ($envVariable, $bootstrap) {
            $bucket->getSource()->writeAll(
                '<?php' . "\n" .
                'require_once \'' . $bootstrap . '\';' . "\n" .
                '$class = getenv(\'' . $envVariable . '\');' . "\n" .
                'if(class_exists(\'\mageekguy\atoum\scripts\runner\', false))' . "\n" .
                '    \mageekguy\atoum\scripts\runner::disableAutorun();' . "\n" .
                '$reflection = new \Atoum\PraspelExtension\Praspel\Reflection\RClass($class);' . "\n" .
                'echo serialize($reflection), "\n";'
            );

            return false;
        });
        $reflectionner->on('output', function (Event\Bucket $bucket) use (&$buffer) {
            $data    = $bucket->getData();
            $buffer .= $data['line'] . "\n";

            return;
        });
        $reflectionner->on('stop', function () use (&$buffer, &$reflection) {
            $handle = @unserialize($buffer);

            if (false === $handle) {
                echo $buffer, "\n";

                return;
            }

            $reflection = $handle;

            return;
        });

        foreach ($classes as $class) {
            putenv($envVariable . '=' . $class);
            $buffer = null;
            $reflectionner->run();

            $output = $generator->generate($reflection);

            if (null === $testRoot) {
                echo $output;

                continue;
            }

            $namespacer   = $generator->getTestNamespacer();
            $outClassname = $namespacer($reflection->getNamespaceName()) . '\\' .
                            trim($reflection->getName(), '\\');
            $filename     = $testRoot . DS .
                            str_replace('\\', DS, $outClassname) . '.php';
            $dirname      = dirname($filename);
            $status       = $class . ' (in ' . $filename . ')';

            echo '  ⌛ ', $status;

            if (false === is_dir($dirname)) {
                mkdir($dirname, 0755, true);
            }

            file_put_contents($filename, $output);

            Console\Cursor::clear('↔');
            echo
                '  ', Console\Chrome\Text::colorize('✔︎', 'foreground(green)'),
                ' ', $status, "\n";
        }

        return;
    }

    /**
     * The command usage.
     *
     * @return  int
     */
    public function usage()
    {
        echo
            'Usage   : generate <options>', "\n",
            'Options :', "\n",
            $this->makeUsageOptionsList([
                'b'    => 'Bootstrap file (load Hoa and atoum).',
                'c'    => 'Class to scan.',
                'n'    => 'Out namespace (by default: tests\praspel).',
                'r'    => 'Root of the out namespace.',
                'help' => 'This help.'
            ]), "\n";

        return;
    }
}
