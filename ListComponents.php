<?php

namespace App\Console\Commands\Common;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListComponents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'component:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all components in the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Components in the application:');

        $componentPath = resource_path('views/components');

        if (File::isDirectory($componentPath)) {
            $components = File::allFiles($componentPath);
            foreach ($components as $component) {
                $path = $component->getPathname();
                $relativePath = str_replace($componentPath . DIRECTORY_SEPARATOR, '', $path);

                if ($component->isFile()) {
                    $this->line($relativePath);
                } elseif ($component->isDir()) {
                    $this->line($relativePath . '/');
                    $nestedFiles = File::files($path);
                    foreach ($nestedFiles as $nestedFile) {
                        $nestedPath = str_replace($componentPath . DIRECTORY_SEPARATOR, '', $nestedFile->getPathname());
                        $this->line("  - " . $nestedPath);
                    }
                }
            }
        } else {
            $this->error('Components directory not found.');
        }
    }

}
