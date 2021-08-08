<?php

namespace Arrilot\Tests\BitrixMigrations;

use Mockery as m;

class InstallCommandTest extends CommandTestCase
{
    protected function mockCommand($database)
    {
        return m::mock('Arrilot\BitrixMigrations\Commands\InstallCommand[abort]', ['migrations', $database])
            ->shouldAllowMockingProtectedMethods();
    }

    public function testItCreatesMigrationTable()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseStorageInterface');
        $database->shouldReceive('checkMigrationTableExistence')->once()->andReturn(false);
        $database->shouldReceive('createMigrationTable')->once();

        $command = $this->mockCommand($database);

        $result = $this->runCommand($command);

        $this->assertSame(0, $result);
    }

    public function testItDoesNotCreateATableIfItExists()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseStorageInterface');
        $database->shouldReceive('checkMigrationTableExistence')->once()->andReturn(true);
        $database->shouldReceive('createMigrationTable')->never();

        $command = $this->mockCommand($database);
        $command->shouldReceive('abort')->once()->andThrow('DomainException');

        $result = $this->runCommand($command);

        $this->assertSame(1, $result);
    }
}
