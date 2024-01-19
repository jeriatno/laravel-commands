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
    
    class ShowFunction extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'show:function {--service=}';
        
        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Show all function in Services';
        
        /**
         * Execute the console command.
         *
         * @throws \ReflectionException
         */
        public function handle(): void
        {
            $service = $this->option('service') ?? null;
            if ($this->areParametersMissing($service)) {
                return;
            }
            
            $service = $this->getServiceInstance($service);
            
            $reflection = new ReflectionClass($service);
            $className = $reflection->getName();
            $methods = $reflection->getMethods();
            
            $this->info('# Methods in ' . $className . ':');
            
            $maxMethodNameLength = max(array_map(fn($method) => strlen($method->name), $methods));
            
            foreach ($methods as $method) {
                if ($method->name !== '__construct') {
                    $methodName = str_pad($method->name, $maxMethodNameLength + 5, ' ');
                    $this->line('- ' . $methodName . $this->getMethodDescription($method));
                }
            }
        }
        
        protected function getMethodDescription(ReflectionMethod $method)
        {
            $docComment = $method->getDocComment();
            
            // Extract the first line of the doc comment as the description
            $descriptionLines = explode("\n", trim($docComment));
            $description = isset($descriptionLines[1]) ? trim($descriptionLines[1], " *\t") : 'No description available';
            
            return "Desc: {$description}";
        }
        
        private function areParametersMissing($service): bool
        {
            $missingParameters = [];
            
            if (is_null($service)) {
                $missingParameters[] = '--service';
            }
            
            if (!empty($missingParameters)) {
                $missingParametersString = implode(', ', $missingParameters);
                $this->error("\nPlease provide the following parameter(s): $missingParametersString");
                
                return true;
            }
            
            return false;
        }
        
        function getServiceInstance($service)
        {
            switch ($service) {
                case 'attendance':
                    return app(AttendanceService::class);
                    break;
                    
                case 'billing':
                    return app(BillingService::class);
                    break;
                    
                case 'module':
                    return app(ModuleService::class);
                    break;
                    
                case 'notification':
                    return app(NotificationService::class);
                    break;
                    
                case 'payment':
                    return app(PaymentService::class);
                    break;
                
                case 'payroll':
                    return app(PayrollService::class);
                    break;
                    
                case 'permission':
                    return app(PermissionService::class);
                    break;
                    
                case 'tasking':
                    return app(TaskingService::class);
                    break;
                
                default:
                    throw new \InvalidArgumentException("Service type '$service' is not supported.");
            }
        }
    }
