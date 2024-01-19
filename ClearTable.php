<?php
    
    namespace App\Console\Commands\Common;
    
    use App\Services\AttendanceService;
    use App\Services\BillingService;
    use App\Services\ModuleService;
    use App\Services\NotificationService;
    use App\Services\PaymentService;
    use App\Services\PayrollService;
    use App\Services\PermissionService;
    use App\Services\TaskingService;
    use Illuminate\Console\Command;
    use ReflectionClass;
    use ReflectionMethod;
    
    class ClearTable extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'clear:table {tableName}';
        
        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Clear table with truncate data';
        
        /**
         * Execute the console command.
         *
         * @throws \ReflectionException
         */
        public function handle(): void
        {
            $tableName = $this->argument('tableName');
            if ($this->areParametersMissing($tableName)) {
                return;
            }
            
            clearTable($tableName);
        }
        private function areParametersMissing($tableName): bool
        {
            $missingParameters = [];
            
            if (is_null($tableName)) {
                $missingParameters[] = 'tableName';
            }
            
            if (!empty($missingParameters)) {
                $missingParametersString = implode(', ', $missingParameters);
                $this->error("\nPlease provide the following parameter(s): $missingParametersString");
                
                return true;
            }
            
            return false;
        }
    }
