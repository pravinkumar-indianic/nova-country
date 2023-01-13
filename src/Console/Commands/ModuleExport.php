<?php

namespace Indianicinfotech\Modules\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ModuleExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:export {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'export module [Country]';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $modules = [
        // 'AdminUser'=>'AdminUser',
        // 'QrCode' => 'QrCode',
        // 'User' => 'User',
        // 'Role' => 'Role',
        'Country' => 'Country',
        // 'State' => 'State',
        // 'City' => 'City',
        // 'Newsletter' => 'Newsletter',
        // 'CmsPage' => 'CmsPage',
        // 'DynamicCmsPage' => 'DynamicCmsPage',
        // 'EmailTemplates' => 'EmailTemplates',
        // 'VariousExamples' => 'VariousExamples',
        // 'Category' => 'Category',
        // 'NewCategory'=>'NewCategory'
    ];

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        $module = $this->argument('module');
        /*
         * validation for module existence
         * */
        if (!in_array($module, $this->modules)) {
            $this->info("{$module} module not exist");
            return false;
        }

        $this->info("exporting {$module} module...");
        try {
            $path = "Indianicinfotech/{$module}";

            if (!is_dir(base_path($path))) {
                if (!is_dir('Indianicinfotech')) {
                    mkdir(base_path("Indianicinfotech"), 0755);
                }
                /*
                 * export directory
                 * */
                File::copyDirectory(__DIR__ . "/../../exports/{$module}/", base_path("{$path}/"));
                File::copy(__DIR__ . "/../../exports/{$module}/Nova/{$module}.php/", app_path("/Nova"));
                /*
                 * autoload directory
                 * */
                exec('composer dump-autoload');

                /*
                * run migration
                * */
                $migrationPath = $path."/migrations";
                if (is_dir(base_path($migrationPath))) {
                    foreach (array_diff(scandir(base_path($migrationPath), SCANDIR_SORT_NONE), [".",".."]) as $migration) {
                        $this->call('migrate', [
                            '--path' => $migrationPath."/".$migration
                        ]);
                    }
                }

                /*
                 * run Seeders
                 * */
                // if(is_dir(base_path($path . "/Seeders"))){
                //     self::addSeedsFrom(base_path($path . "/Seeders"));
                // }

                if (is_dir(base_path($path . "/Seeders"))) {
                    $file_names = glob($path . "/Seeders" . '/*.php');
                    foreach ($file_names as $filename) {
                        $class = basename($filename, '.php');
                        echo "\033[1;33mSeeding:\033[0m {$class}\n";
                        $startTime = microtime(true);
                        Artisan::call('db:seed', [ '--class' =>'Indianicinfotech\\'.$module.'\\Seeders\\'.$class, '--force' => '' ]);
                        $runTime = round(microtime(true) - $startTime, 2);
                        echo "\033[0;32mSeeded:\033[0m {$class} ({$runTime} seconds)\n";
                    }
                }

                $this->info("{$module} module exported");
            } else {
                $this->info("{$module} module is already exist");
            }
        } catch (Exception $e) {
            $this->info($e->getMessage());
        }
        return true;
    }
}
