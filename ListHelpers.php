<?php

namespace App\Console\Commands\Common;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListHelpers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helper:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all helper functions in the application';

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
        $this->info('Helper functions in the application:'."\n");

        $helperFilePath = app_path('Helpers/helper.php');

        if (File::exists($helperFilePath)) {
            $content = File::get($helperFilePath);

            // Extract functions and their comments
            $pattern = '/\/\*\*(.*?)\*\/\s*function\s+(\w+)\s*\(/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $comment = trim(preg_replace('/^\s*\*\s?/', '', $match[1] ?? ''));

                    // Extract only the first line of the comment
                    $commentLines = explode("\n", $comment);
                    $firstLine = trim($commentLines[0] ?? '');

                    $functionName = $match[2];

                    // Calculate padding to align descriptions
                    $padding = str_repeat(' ', max(0, 40 - strlen($functionName)));

                    // Display function name, padding, and the first line of the comment
                    $this->line("{$functionName}{$padding} - {$firstLine}");
                }
            } else {
                $this->error('No helper functions found in helper.php.');
            }
        } else {
            $this->error('helper.php file not found.');
        }
    }
}
