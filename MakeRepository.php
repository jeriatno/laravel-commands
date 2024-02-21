<?php

namespace App\Console\Commands\Common;

use Illuminate\Console\Command;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name} {--base}';

    protected $description = 'Create a new Repository class';

    protected function getStub($type): string
    {
        return file_get_contents(base_path(sprintf('stubs/%s.stub', $type)));
    }

    protected function generate(string $name, string $type, string $typeFolder)
    {
        $typeCap    = ucfirst($type) ;
        $explode    = explode('/', $name);
        $directory  = implode('\\', array_slice($explode, 0, -1));
        $className  = end($explode);

        $namespace         = sprintf('%s%s', $typeFolder, $directory ? sprintf('\%s', $directory) : '');
        $namespaceContract = sprintf('%s\%s', 'Contracts', $directory ? sprintf('%s\%s', $directory, $className) : $className);
        $namespaceModel    = sprintf('%s\%s', 'Models', $directory ? sprintf('%s\%s', $directory, $className) : $className);

        $fullDirectory = app_path($namespace);
        $fileName      = sprintf('%s%s.php', end($explode), $typeCap);
        $fullPath      = sprintf('%s/%s', $fullDirectory, $fileName);

        if ($this->option('base')){
            $template = str_replace(
                ['{{name}}', '{{namespace}}', '{{namespaceContract}}', '{{namespaceModel}}'],
                [$className, $namespace, $namespaceContract, $namespaceModel],
                $this->getStub($type)
            );
        } else {
            $template = str_replace(
                ['{{name}}', '{{namespace}}', '{{namespaceContract}}'],
                [$className, $namespace, $namespaceContract],
                $this->getStub($type.'.blank')
            );
        }
        
        $fullDirectory = str_replace('\\', '/', $fullDirectory);
        $fullPath = str_replace('\\', '/', $fullPath);

        if (!file_exists($fullDirectory)) mkdir($fullDirectory, 0777, true);

        if (!file_exists($fullPath)) {
            file_put_contents($fullPath, $template);
            $this->info(sprintf('%s created successfully.', $typeCap));
        } else {
            $this->error(sprintf('The given %s already exists', $typeCap));
        }
    }

    protected function addBind(string $name)
    {
        $path = app_path('Providers/RepositoryServiceProvider.php');
        $file = file_get_contents($path);

        $explode = explode('/', $name);
        $import  = implode('\\', $explode);
        $class   = end($explode);

        $importRepo     = sprintf('use App\Repositories\%sRepository;', $import);
        $importContract = sprintf('use App\Contracts\%sContract;', $import);

        $bind = sprintf('$this->app->bind(%1$sContract::class, %1$sRepository::class);', $class);

        $search = ['// {{ import }}', '// {{ bind }}'];
        $replace = [
            $importRepo . PHP_EOL . $importContract . PHP_EOL . '// {{ import }}',
            $bind . PHP_EOL . '        // {{ bind }}'
        ];

        $replaced = str_replace($search, $replace, $file);
        file_put_contents($path, $replaced);
    }

    public function handle()
    {
        $name = $this->argument('name');

        $this->generate($name, 'contract', 'Contracts');
        $this->generate($name, 'repository', 'Repositories');

        $this->addBind($name);
    }
}
