<?php

namespace App\Console\Commands\Common;

use App\Models\GlobalNumbering;
use App\Traits\GenerateNumber;
use Illuminate\Console\Command;
use function App\Console\Commands\dd;

class MakeNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:number {for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for generate number';

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
        $for = $this->argument('for');

        if ($for == 'principalClaim') {
            $number = GenerateNumber::PrincipalClaim('alphabet');
        }

        if ($for == 'forwardOrder') {
            $data = GenerateNumber::get('ForwardOrder');

            $sequence = GlobalNumbering::where('prefix', $data->prefix)->lockForUpdate()->firstOr(function () use ($data) {
                return GlobalNumbering::create(['prefix' => $data->prefix]);
            });

            $number = GenerateNumber::forwardOrder($sequence->number, $data->clause);
        }

        echo $number;
    }
}
