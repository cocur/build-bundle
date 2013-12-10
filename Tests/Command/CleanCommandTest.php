<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Tests\Command;

use \Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Cocur\Bundle\BuildBundle\Command\CleanCommand;

/**
 * CleanCommandTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class CleanCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    private $application;

    /** @var Symfony\Component\Filesystem\Filesystem */
    private $filesystem;

    /** @var vfsStreamDirectory */
    private $buildDir;

    public function setUp()
    {
        $this->buildDir = new vfsStreamDirectory('build');
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot($this->buildDir);

        $this->filesystem = m::mock('Symfony\Component\Filesystem\Filesystem');

        $this->application = new Application($this->getMockKernel());
        $this->application->add(new CleanCommand($this->filesystem, $this->buildDir->url()));
    }

    /**
     * @test
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::__construct()
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::configure()
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::execute()
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::cleanDirectory()
     */
    public function executeShouldRunCommand()
    {
        // Create mock files
        $this->buildDir->addChild(vfsStream::newFile('foo1.txt'));
        $this->buildDir->addChild($barDir = vfsStream::newDirectory('bar'));
        $barDir->addChild(vfsStream::newFile('foo2.txt'));

        $this->filesystem->shouldReceive('remove')->with(sprintf('%s/foo1.txt', $this->buildDir->url()))->once();
        $this->filesystem->shouldReceive('remove')->with(sprintf('%s/bar/foo2.txt', $this->buildDir->url()))->once();
        $this->filesystem->shouldReceive('remove')->with(sprintf('%s/bar', $this->buildDir->url()))->once();

        $command = $this->application->find('cocur:clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ]);

        $this->assertRegExp('/Removed 3 files from/', $commandTester->getDisplay());
    }

    /**
     * @test
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::__construct()
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::configure()
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::execute()
     * @covers Cocur\Bundle\BuildBundle\Command\CleanCommand::cleanDirectory()
     */
    public function executeShouldOutputDetailedInformationInVerboseMode()
    {
        $this->buildDir->addChild(vfsStream::newFile('foo1.txt'));
        $this->buildDir->addChild($barDir = vfsStream::newDirectory('bar'));
        $barDir->addChild(vfsStream::newFile('foo2.txt'));

        $this->filesystem->shouldReceive('remove')->with(sprintf('%s/foo1.txt', $this->buildDir->url()))->once();
        $this->filesystem->shouldReceive('remove')->with(sprintf('%s/bar/foo2.txt', $this->buildDir->url()))->once();
        $this->filesystem->shouldReceive('remove')->with(sprintf('%s/bar', $this->buildDir->url()))->once();

        $command = $this->application->find('cocur:clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ], [ 'verbosity' => 2 ]);

        $this->assertRegExp('/Delete:(.*)foo1\.txt/', $commandTester->getDisplay());
        $this->assertRegExp('/Delete:(.*)bar/', $commandTester->getDisplay());
        $this->assertRegExp('/Delete:(.*)bar\/foo2\.txt/', $commandTester->getDisplay());
        $this->assertRegExp('/Removed 3 files from/', $commandTester->getDisplay());
    }

    /**
     * @return Symfony\Component\HttpKernel\KernelInterface
     */
    protected function getMockKernel()
    {
        $kernel = m::mock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->shouldReceive('getName')->andReturn('app');
        $kernel->shouldReceive('getEnvironment')->andReturn('prod');
        $kernel->shouldReceive('isDebug')->andReturn(false);

        return $kernel;
    }
}
