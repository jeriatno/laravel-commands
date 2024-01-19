<?php

namespace App\Console\Commands\Common;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function App\Console\Commands\public_path;

class ListHelperJs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helperjs:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all helper functions in the JavaScript file';

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
        $this->info('Helper functions in the JavaScript file:');

        $jsHelperFilePath = public_path('js/helpers.js');

        if (File::exists($jsHelperFilePath)) {
            $content = File::get($jsHelperFilePath);

            $functionPattern = '/function\s+(\w+)\s*\(/';

            if (preg_match_all($functionPattern, $content, $matches)) {
                $jsHelperFunctions = $matches[1];

                foreach ($jsHelperFunctions as $jsHelperFunction) {
                    $this->line($jsHelperFunction);
                }
            } else {
                $this->error('No helper functions found in helpers.js.');
            }
        } else {
            $this->error('helpers.js file not found.');
        }
    }
}
