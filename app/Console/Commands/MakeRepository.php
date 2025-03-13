<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Create a new repository class in app/repositories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Repositories/{$name}.php");

        if (File::exists($path)) {
            $this->error('O arquivo já existe!');
            return;
        }

        $template = "<?php

namespace App\Repositories;

class {$name}
{
    //
}";

        File::ensureDirectoryExists(app_path('Repositories'));
        File::put($path, $template);

        $this->info("Repositories {$name} criada com sucesso!");
    }
}
