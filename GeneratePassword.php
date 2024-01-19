<?php
    
    namespace App\Console\Commands\Common;
    
    use App\Models\User;
    use Carbon\Carbon;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Hash;
    
    class GeneratePassword extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'generate:password {--userId=} {--free} {pass?}';
        
        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Command to generate a random password';
        
        /**
         * Execute the console command.
         */
        public function handle()
        {
            $userId = $this->option('userId');
            $free = $this->option('free') ?? null;
            $pass = $this->argument('pass') ?? null;
            
            if ($this->areParametersMissing($userId)) {
                return;
            }
            
            if ($free) {
                $password = $pass;
            }
            else {
                $password = $this->generateRandomPassword();
            }
            
            $this->updateUserPassword($userId, $password);
            $this->info('Generated Password: ' . $password);
        }
        
        function generateRandomPassword($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = str_shuffle($characters);
        
            return substr($randomString, 0, $length);
        }
        
        private function updateUserPassword($userId, $password)
        {
            $user = User::find($userId);
            
            if ($user) {
                $user->password = Hash::make($password);
                $user->password_changed_at = Carbon::now()->toDateTimeString();
                $user->save();
                
                $this->info('User password updated successfully.');
            }
            else {
                $this->error('No user found to update password.');
            }
        }
        
        private function areParametersMissing($userId): bool
        {
            $missingParameters = [];
            
            if (is_null($userId)) {
                $missingParameters[] = '--userId';
            }
            
            if (!empty($missingParameters)) {
                $missingParametersString = implode(', ', $missingParameters);
                $this->error("\nPlease provide the following parameter(s): $missingParametersString");
                
                return true;
            }
            
            return false;
        }
    }
