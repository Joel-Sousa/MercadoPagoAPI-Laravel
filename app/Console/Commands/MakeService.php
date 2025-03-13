<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class in app/services';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (File::exists($path)) {
            $this->error('O arquivo já existe!');
            return;
        }

        $template = "<?php

namespace App\Services;

class {$name}
{
    //
}";

        File::ensureDirectoryExists(app_path('Services'));
        File::put($path, $template);

        $this->info("Services {$name} successfully created!");
    }
}
