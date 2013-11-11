<?php

/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Command;

use \Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand;

/**
 * CleanCommandTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class CleanCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private $fixtures;

    public function setUp()
    {
        $this->fixtures = sprintf('%s/fixtures', __DIR__);

        $this->kernel = m::mock('Symfony\Component\HttpKernel\KernelInterface');
        $this->kernel->shouldReceive('getName')->andReturn('app');
        $this->kernel->shouldReceive('getEnvironment')->andReturn('prod');
        $this->kernel->shouldReceive('isDebug')->andReturn(false);

        mkdir($this->fixtures);
        file_put_contents(sprintf('%s/foo1.txt', $this->fixtures), '');
        mkdir(sprintf('%s/bar', $this->fixtures));
        file_put_contents(sprintf('%s/bar/foo2.txt', $this->fixtures), '');
    }

    public function tearDown()
    {
        if (true === file_exists(sprintf('%s/foo1.txt', $this->fixtures))) {
            unlink(sprintf('%s/foo1.txt', $this->fixtures));
        }
        if (true === file_exists(sprintf('%s/bar/foo2.txt', $this->fixtures))) {
            unlink(sprintf('%s/bar/foo2.txt', $this->fixtures));
        }
        if (true === file_exists(sprintf('%s/bar', $this->fixtures))) {
            rmdir(sprintf('%s/bar', $this->fixtures));
        }
        if (true === file_exists($this->fixtures)) {
            rmdir($this->fixtures);
        }
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::execute()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::cleanDirectory()
     */
    public function testExecute()
    {
        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new CleanCommand($this->fixtures));

        $command = $application->find('braincrafted:static-site:clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ]);

        $this->assertRegExp('/Removed 3 files from/', $commandTester->getDisplay());

        $this->assertFalse(file_exists(sprintf('%s/foo1.txt', $this->fixtures)));
        $this->assertFalse(file_exists(sprintf('%s/bar', $this->fixtures)));
        $this->assertFalse(file_exists(sprintf('%s/bar/foo2.txt', $this->fixtures)));
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::execute()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\CleanCommand::cleanDirectory()
     */
    public function testExecuteVerbose()
    {
        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new CleanCommand($this->fixtures));

        $command = $application->find('braincrafted:static-site:clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName() ], [ 'verbosity' => 2 ]);

        $this->assertRegExp('/Delete:(.*)foo1\.txt/', $commandTester->getDisplay());
        $this->assertRegExp('/Delete:(.*)bar/', $commandTester->getDisplay());
        $this->assertRegExp('/Delete:(.*)bar\/foo2\.txt/', $commandTester->getDisplay());
        $this->assertRegExp('/Removed 3 files from/', $commandTester->getDisplay());

        $this->assertFalse(file_exists(sprintf('%s/foo1.txt', $this->fixtures)));
        $this->assertFalse(file_exists(sprintf('%s/bar', $this->fixtures)));
        $this->assertFalse(file_exists(sprintf('%s/bar/foo2.txt', $this->fixtures)));
    }
}
