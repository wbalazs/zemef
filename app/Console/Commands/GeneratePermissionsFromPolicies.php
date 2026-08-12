<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class GeneratePermissionsFromPolicies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:generate-from-policies
                            {--skip-existing : Skip inserting permissions that already exist in the DB}
                            {--output-sql : Only output SQL without executing}
                            {--table=permissions : The permissions table name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SQL INSERT statements for permissions based on Policy classes in app/Policies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $policiesPath = app_path('Policies');
        if (!File::exists($policiesPath)) {
            $this->error('Policies directory not found: ' . $policiesPath);
            return 1;
        }

        $files = File::allFiles($policiesPath);
        $permissions = [];
        $skippedCount = 0;

        foreach ($files as $file) {
            $class = $this->getClassFromFile($file);
            if (!$class) {
                continue;
            }

            $policyAbilities = $this->getPolicyAbilities($class);
            if (empty($policyAbilities)) {
                continue;
            }

            $modelClass = $this->resolveModelClass($class);
            if (!$modelClass) {
                $this->warn("Could not resolve model class for policy: {$class}");
                continue;
            }

            foreach ($policyAbilities as $ability) {
                $permissions[] = [
                    'ability' => $ability,
                    'model'   => $modelClass,
                ];
            }
        }

        if (empty($permissions)) {
            $this->info('No permissions found.');
            return 0;
        }

        // Remove duplicates (same ability + model)
        $permissions = collect($permissions)->unique(function ($item) {
            return $item['ability'] . '@@' . $item['model'];
        })->values()->toArray();

        $table = $this->option('table');
        $skipExisting = $this->option('skip-existing');
        $outputOnly = $this->option('output-sql');

        if ($outputOnly) {
            $this->outputSql($permissions, $table, $skipExisting);
            return 0;
        }

        // Execute inserts directly (if --output-sql not used)
        $inserted = 0;
        foreach ($permissions as $perm) {
            $exists = \DB::table($table)
                ->where('ability', $perm['ability'])
                ->where('model', $perm['model'])
                ->exists();

            if ($exists && $skipExisting) {
                $skippedCount++;
                continue;
            }

            if ($exists && !$skipExisting) {
                // Update? We'll just skip or you can update. Default: skip.
                $this->line("Permission already exists: {$perm['ability']} on {$perm['model']} – skipping");
                $skippedCount++;
                continue;
            }

            \DB::table($table)->insert($perm);
            $inserted++;
        }

        $this->info("Inserted {$inserted} new permissions. Skipped {$skippedCount}.");
        return 0;
    }

    /**
     * Get fully qualified class name from a file.
     */
    private function getClassFromFile($file)
    {
        $namespace = $this->getNamespace($file->getPathname());
        $class = $namespace . '\\' . $file->getBasename('.php');
        if (!class_exists($class)) {
            return null;
        }
        return $class;
    }

    /**
     * Get namespace from a PHP file.
     */
    private function getNamespace($path)
    {
        $content = file_get_contents($path);
        if (preg_match('/namespace\s+(.+?);/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    /**
     * Get public methods of a policy that represent abilities.
     * Excludes constructor, before, after, and methods starting with underscore.
     */
    private function getPolicyAbilities(string $policyClass): array
    {
        $reflection = new ReflectionClass($policyClass);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        $abilities = [];
        foreach ($methods as $method) {
            $name = $method->getName();
            // Skip typical non-ability methods
            if (in_array($name, ['__construct', 'before', 'after'])) {
                continue;
            }
            if (Str::startsWith($name, '_')) {
                continue;
            }
            // Also skip methods that are likely not abilities (e.g., "viewAny", but we include anyway)
            $abilities[] = $name;
        }
        return $abilities;
    }

    /**
     * Resolve the model class name from a policy class.
     * Converts: App\Policies\Sub\Path\SomePolicy -> App\Models\Sub\Path\Some
     */
    private function resolveModelClass(string $policyClass): ?string
    {
        // Remove the base App\Policies\ part
        if (!Str::startsWith($policyClass, 'App\\Policies\\')) {
            return null;
        }

        $relative = substr($policyClass, strlen('App\\Policies\\'));
        // Remove 'Policy' suffix from the last part
        $parts = explode('\\', $relative);
        $last = array_pop($parts);
        if (Str::endsWith($last, 'Policy')) {
            $last = substr($last, 0, -6);
        } else {
            // If no Policy suffix, keep as is but unlikely
        }
        $parts[] = $last;
        $modelRelative = implode('\\', $parts);

        // Try common model base namespaces
        $candidates = [
            'App\\Models\\' . $modelRelative,
            'App\\Models\\' . str_replace('\\', '\\\\', $modelRelative), // already escaped
        ];

        foreach ($candidates as $candidate) {
            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        // Fallback: try singularizing if it's a plural folder? Not needed.
        return null;
    }

    /**
     * Output SQL INSERT statements.
     */
    private function outputSql(array $permissions, string $table, bool $skipExisting): void
    {
        $lines = [];
        $lines[] = "-- Generated on " . now();
        $lines[] = "-- Table: {$table}";
        $lines[] = "";

        if ($skipExisting) {
            $lines[] = "INSERT IGNORE INTO `{$table}` (`ability`, `model`) VALUES";
        } else {
            $lines[] = "INSERT INTO `{$table}` (`ability`, `model`) VALUES";
        }

        $values = [];
        foreach ($permissions as $perm) {
            $ability = addslashes($perm['ability']);
            $model = addslashes($perm['model']);
            $values[] = "('{$ability}', '{$model}')";
        }

        $lines[] = implode(",\n", $values) . ";";
        $this->line(implode("\n", $lines));
    }
}