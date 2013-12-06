<?php

/**
 * This file is part of BraincraftedCocurBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\CocurBundle\Tests\Command;

use \Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\AsseticBundle\Command\DumpCommand as AsseticDumpCommand;;
use Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Braincrafted\Bundle\CocurBundle\Command\BuildCommand;

/**
 * BuildCommandTest
 *
 * @category   Test
 * @package    BraincraftedCocurBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class BuildCommandTest extends \PHPUnit_Framework_TestCase
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
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoBuildDirectory()
    {
        new BuildCommand(m::mock('Braincrafted\Bundle\CocurBundle\Renderer\RoutesRenderer'), []);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::__construct()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::configure()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::execute()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeAssetsInstall()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeAsseticDump()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeCommand()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::getBaseUrl()
     */
    public function executeShouldRunCommand()
    {
        $renderer = m::mock('Braincrafted\Bundle\CocurBundle\Renderer\RoutesRenderer');
        $renderer->shouldReceive('render')->once()->andReturn(1);

        $assetsInstallCommand = new AssetsInstallCommand();
        $asseticDumpCommand = new AsseticDumpCommand();

        $this->application->add(new BuildCommand($renderer, [
            'build_directory' => $this->buildDir->url(),
            'enable_assetic'  => true
        ]));
        $this->application->add($assetsInstallCommand);
        $this->application->add($asseticDumpCommand);
        $command = $this->application->find('cocur:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ]);

        $this->assertRegExp('/Rendered 1 routes/', $commandTester->getDisplay());
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::__construct()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::configure()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::execute()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeAssetsInstall()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeAsseticDump()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeCommand()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::getBaseUrl()
     */
    public function executeShouldRunCommandWithHighVerbosity()
    {
        $renderer = m::mock('Braincrafted\Bundle\CocurBundle\Renderer\RoutesRenderer');
        $renderer->shouldReceive('render')->once()->andReturn(1);

        $assetsInstallCommand = new AssetsInstallCommand();
        $asseticDumpCommand = new AsseticDumpCommand();

        $this->application->add(new BuildCommand($renderer, [
            'build_directory' => $this->buildDir->url(),
            'enable_assetic'  => true
        ]));
        $this->application->add($assetsInstallCommand);
        $this->application->add($asseticDumpCommand);
        $command = $this->application->find('cocur:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ], [ 'verbosity' => 2 ]);

        $this->assertRegExp('/Rendered 1 routes/', $commandTester->getDisplay());
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::__construct()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::configure()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::execute()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeAssetsInstall()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeAsseticDump()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::executeCommand()
     * @covers Braincrafted\Bundle\CocurBundle\Command\BuildCommand::getBaseUrl()
     */
    public function executeShouldRunCommandWithBaseUrl()
    {
        $this->buildDir->addChild(vfsStream::newDirectory('subdir'));

        $renderer = m::mock('Braincrafted\Bundle\CocurBundle\Renderer\RoutesRenderer');
        $renderer->shouldReceive('render')->once()->andReturn(1);
        $renderer->shouldReceive('setBaseUrl')->with('/subdir')->once();

        $assetsInstallCommand = new AssetsInstallCommand();
        $asseticDumpCommand = new AsseticDumpCommand();

        $this->application->add(new BuildCommand($renderer, [
            'build_directory' => $this->buildDir->url(),
            'enable_assetic'  => true
        ]));
        $this->application->add($assetsInstallCommand);
        $this->application->add($asseticDumpCommand);
        $command = $this->application->find('cocur:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName(), '--base-url' => '/subdir' ]);

        $this->assertRegExp('/Rendered 1 routes/', $commandTester->getDisplay());
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
        $kernel->shouldReceive('getContainer')->andReturn($this->getMockContainer($kernel));
        $kernel->shouldReceive('getBundles')->andReturn([]);

        return $kernel;
    }

    /**
     * @param Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getMockContainer($kernel)
    {
        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->shouldReceive('get')->with('filesystem')->andReturn($this->getMockFilesystem());
        $container->shouldReceive('get')->with('kernel')->andReturn($kernel);
        $container->shouldReceive('get')->with('assetic.asset_manager')->andReturn($this->getMockAssetManager());

        return $container;
    }

    /**
     * @return Symfony\Component\Filesystem\Filesystem
     */
    protected function getMockFilesystem()
    {
        $filesystem = m::mock('Symfony\Component\Filesystem\Filesystem');
        $filesystem->shouldReceive('mkdir');

        return $filesystem;
    }

    protected function getMockAssetManager()
    {
        $assetManager = m::mock('Assetic\AssetManager');
        $assetManager->shouldReceive('isDebug')->andReturn(false);
        $assetManager->shouldReceive('getNames')->andReturn([]);

        return $assetManager;
    }
}
