<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Interfaces\DatabaseStorageInterface;
use Arrilot\BitrixMigrations\Migrator;

/**
 * Перед выполнением миграций проверяет чтобы в бд была создана таблица с миграциями
 */
class SmartMigrateCommand extends MigrateCommand
{
    /**
     * @var DatabaseStorageInterface
     */
    private $database;

    public function __construct(Migrator $migrator, DatabaseStorageInterface $database)
    {
        parent::__construct($migrator, 'migrate');
        $this->database = $database;
    }

    protected function fire(): int
    {
        if (!$this->database->checkMigrationTableExistence()) {
            $this->database->createMigrationTable();
        }

        return parent::fire();
    }
}
