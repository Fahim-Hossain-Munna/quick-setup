<?php

namespace Fahimhossainmunna\QuickSetup\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use PDO;
use Exception;

class QuickSetupCommand extends Command
{
    protected $signature = 'run:quick-setup';
    protected $description = 'Quick setup: copy .env, set app values, run composer/npm, migrations, storage:link, clear caches';

    public function handle()
    {
        $this->info('🚀 Quick setup started...');

        // 1) Copy .env.example -> .env
        $envExample = base_path('.env.example');
        $envPath = base_path('.env');

        if (!file_exists($envExample)) {
            $this->error('.env.example not found!');
            return 1;
        }

        if (file_exists($envPath)) {
            if ($this->confirm('.env already exists. Overwrite?', false)) {
                copy($envExample, $envPath);
                $this->info('✅ .env overwritten.');
            } else {
                $this->info('Keeping existing .env.');
            }
        } else {
            copy($envExample, $envPath);
            $this->info('✅ .env created from .env.example.');
        }

        // 2) Ask for inputs
        $appName = $this->ask('App name', 'Laravel Project');
        $appUrl  = $this->ask('App URL', 'http://localhost');

        $dbHost = $this->ask('Database host', '127.0.0.1');
        $dbPort = $this->ask('Database port', '3306');
        $dbName = $this->ask('Database name', 'laravel');
        $dbUser = $this->ask('Database username', 'root');
        $dbPass = $this->secret('Database password (leave blank if none)');

        // 3) Update .env values
        $this->setEnvValue('APP_NAME', "\"{$appName}\"");
        $this->setEnvValue('APP_URL', $appUrl);
        $this->setEnvValue('DB_HOST', $dbHost);
        $this->setEnvValue('DB_PORT', $dbPort);
        $this->setEnvValue('DB_DATABASE', $dbName);
        $this->setEnvValue('DB_USERNAME', $dbUser);
        $this->setEnvValue('DB_PASSWORD', $dbPass ?? '');

        $this->info('✅ .env file updated successfully.');

        // 4) composer install
        if ($this->confirm('Run composer install?', true)) {
            $this->runProcess(['composer', 'install']);
        }

        // 5) npm install
        if ($this->confirm('Run npm install?', true)) {
            $this->runProcess(['npm', 'install']);
        }

        // 6) npm run build
        if ($this->confirm('Run npm run build?', true)) {
            $this->runProcess(['npm', 'run', 'build']);
        }

        // 7) Check if database exists
        $dbExists = $this->checkDatabaseExists($dbHost, $dbPort, $dbUser, $dbPass, $dbName);

        // 8) Run migrations
        if ($dbExists) {
            $this->info("Database '{$dbName}' exists. Running migrate:fresh --seed...");
            $this->runProcess(['php', 'artisan', 'migrate:fresh', '--seed', '--force']);
        } else {
            $this->info("Database '{$dbName}' not found. Running migrate --seed...");
            $this->runProcess(['php', 'artisan', 'migrate', '--seed', '--force']);
        }

        // 9) storage link
        $this->runProcess(['php', 'artisan', 'storage:link']);

        // 10) clear caches
        $this->runProcess(['php', 'artisan', 'config:clear'], false);
        $this->runProcess(['php', 'artisan', 'cache:clear'], false);
        $this->runProcess(['php', 'artisan', 'route:clear'], false);
        $this->runProcess(['php', 'artisan', 'view:clear'], false);

        $this->info('🎉 Quick setup completed successfully!');
        return 0;
    }

    protected function setEnvValue($key, $value)
    {
        $path = base_path('.env');
        $content = file_exists($path) ? file_get_contents($path) : '';
        $pattern = "/^{$key}=.*$/m";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "{$key}={$value}", $content);
        } else {
            $content .= PHP_EOL . "{$key}={$value}" . PHP_EOL;
        }

        file_put_contents($path, $content);
    }

    protected function runProcess(array $command, $throwOnFail = true)
    {
        $this->line('> ' . implode(' ', $command));
        $process = new Process($command, base_path());
        $process->setTimeout(3600);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful() && $throwOnFail) {
            throw new ProcessFailedException($process);
        }
    }

    protected function checkDatabaseExists($host, $port, $user, $pass, $db)
    {
        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :db");
            $stmt->execute(['db' => $db]);
            return (bool) $stmt->fetch();
        } catch (Exception $e) {
            $this->warn("⚠️ DB check failed: " . $e->getMessage());
            return false;
        }
    }
}
