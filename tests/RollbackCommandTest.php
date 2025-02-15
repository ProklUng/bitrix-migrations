<?php

namespace Arrilot\Tests\BitrixMigrations;

use Mockery as m;

class RollbackCommandTest extends CommandTestCase
{
    protected function mockCommand($migrator)
    {
        $command = 'Arrilot\BitrixMigrations\Commands\RollbackCommand[abort, info, message, getMigrationObjectByFileName,markRolledBackWithConfirmation]';

        return m::mock($command, [$migrator])->shouldAllowMockingProtectedMethods();
    }

    public function testItRollbacksNothingIfThereIsNoMigrations()
    {
        $migrator = m::mock('Arrilot\BitrixMigrations\Migrator');
        $migrator->shouldReceive('getRanMigrations')->once()->andReturn([]);
        $migrator->shouldReceive('rollbackMigration')->never();
        $migrator->shouldReceive('hardRollbackMigration')->never();

        $command = $this->mockCommand($migrator);
        $command->shouldReceive('info')->with('Nothing to rollback')->once();

        $result = $this->runCommand($command);

        $this->assertSame(1, $result);
    }

    public function testItRollsBackTheLastMigration()
    {
        $migrator = m::mock('Arrilot\BitrixMigrations\Migrator');
        $migrator->shouldReceive('getRanMigrations')->once()->andReturn([
            '2014_11_26_162220_foo',
            '2015_11_26_162220_bar',
        ]);
        $migrator->shouldReceive('doesMigrationFileExist')->once()->andReturn(true);
        $migrator->shouldReceive('rollbackMigration')->once();
        $migrator->shouldReceive('hardRollbackMigration')->never();

        $command = $this->mockCommand($migrator);
        $command->shouldReceive('info')->with('Nothing to rollback')->never();
        $command->shouldReceive('message')->with('<info>Rolled back:</info> 2015_11_26_162220_bar.php')->once();

        $result = $this->runCommand($command);

        $this->assertSame(0, $result);
    }

    public function testItRollbackNonExistingMigration()
    {
        $migrator = m::mock('Arrilot\BitrixMigrations\Migrator');
        $migrator->shouldReceive('getRanMigrations')->once()->andReturn([
            '2014_11_26_162220_foo',
            '2015_11_26_162220_bar',
        ]);
        $migrator->shouldReceive('doesMigrationFileExist')->once()->andReturn(false);
        $migrator->shouldReceive('rollbackMigration')->never();
        $migrator->shouldReceive('hardRollbackMigration')->never();

        $command = $this->mockCommand($migrator);
        $command->shouldReceive('markRolledBackWithConfirmation')->with('2015_11_26_162220_bar')->once();
        $command->shouldReceive('info')->with('Nothing to rollback')->never();
        $command->shouldReceive('message')->with('<info>Rolled back:</info> 2015_11_26_162220_bar.php')->once();

        $result = $this->runCommand($command);

        $this->assertSame(0, $result);
    }
}
