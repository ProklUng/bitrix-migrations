<?php

namespace Arrilot\BitrixMigrations\Interfaces;

interface MigrationInterface
{
    /**
     * Run the migration.
     *
     * @return void|false Return false to stop the migration process
     */
    public function up();

    /**
     * Reverse the migration.
     *
     * @return void|false Return false to stop the migration process
     */
    public function down();

    /**
     * use transaction
     *
     * @return bool
     */
    public function useTransaction($default = false);
}
