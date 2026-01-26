<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Runs database/thingy-seed.sql as-is.
     * The SQL reads current_setting('app.schema', true) and defaults to 'thingy'.
     */
    public function run(): void
    {
        // Get schema name from environment (e.g., .env or .env.testing)
        $schema = env('DB_SCHEMA');

        // 1. Create schema and tables (database.sql)
        $schemaPath = base_path('database/database.sql');
        if (file_exists($schemaPath)) {
            $sql = file_get_contents($schemaPath);
            DB::unprepared($sql);
            $this->command?->info('Tables created successfully');
        } else {
            $this->command?->error('database.sql not found!');
        }

        // 2. Populate with sample data (populate.sql)
        $populatePath = base_path('database/populate.sql');
        if (file_exists($populatePath)) {
            $sql = file_get_contents($populatePath);
            DB::unprepared($sql);
            $this->command?->info('Sample data inserted successfully');
        } else {
            $this->command?->warn('populate.sql not found - skipping sample data');
        }

        // Show a message in the Artisan console
        $this->command?->info('Database seeded using schema: ' . ($schema ?? 'thingy (default)'));
    }
}
