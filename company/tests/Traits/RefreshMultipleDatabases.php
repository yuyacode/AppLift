<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Contracts\Console\Kernel;

trait RefreshMultipleDatabases
{
    use RefreshDatabase {
        refreshTestDatabase   as baseRefreshTestDatabase;
        migrateFreshUsing     as baseMigrateFreshUsing;
        connectionsToTransact as baseConnectionsToTransact;
    }

    private static $targetDBList = ['company', 'student', 'common'];

    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            foreach (self::$targetDBList as $db) {
                $this->artisan('migrate:fresh', $this->migrateFreshUsing($db));

                $this->app[Kernel::class]->setArtisan(null);
            }

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    protected function migrateFreshUsing($db)
    {
        $options = $this->baseMigrateFreshUsing();

        $options['--database'] = $db;
        $options['--path']     = 'database/migrations/'. $db;
        $options['--force']    = true;

        return $options;
    }

    protected function connectionsToTransact()
    {
        return self::$targetDBList;
    }
}
