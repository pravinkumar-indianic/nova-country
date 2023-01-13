<?php

namespace Indianicinfotech\Modules;

use Illuminate\Support\ServiceProvider;
use Indianicinfotech\Modules\Console\Commands\ModuleExport;

class CountryServiceProvider extends ServiceProvider
{
    /**
      * Register services.
      *
      * @return void
      */
    public function register()
    {
        //
    }

     /**
    * Bootstrap services.
    *
    * @return void
    */
     public function boot()
     {
         self::configureModule([
             // 'AdminUser',
             // 'QrCode',
             // 'Role',
             // 'User',
             'Country',
             // 'State',
             // 'City',
             // 'Newsletter',
             // 'CmsPage',
             // 'DynamicCmsPage',
             // 'EmailTemplates',
             // 'VariousExamples',
             // 'Category',
             // 'NewCategory'
         ]);

         $this->commands([
             ModuleExport::class,
         ]);
     }

     public function configureModule(array $dirs): void
     {
         foreach ($dirs as $dir) {
             $path = "Indianicinfotech/{$dir}";
             if (is_dir(base_path($path))) {
                 // $this->loadRoutesFrom(base_path("{$path}/routes/web.php"));
                 // $this->loadViewsFrom(base_path("{$path}/views"), $dir);
                 $this->loadMigrationsFrom(base_path("{$path}/migrations"));
             }
         }
     }
}
