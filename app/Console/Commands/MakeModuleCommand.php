<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;


class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name} {--m}';
    protected $description = 'Generate migration, model, controller, request, seeder,factory , routes , repository, and service for a module';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $lowercase_name = lcfirst($this->argument('name'));
        $nameController = $name . "Controller";

        if ($this->option('m') == 1) {
            // Generate Model
            Artisan::call("make:model {$name} -m");
            $this->warn("1. ");
            $this->info("Model {$name} and Migration {$name} created successfully!");
        } else {
            $this->warn("1. ");
            $this->warn("Model {$name} and Migration {$name} not created in this command!");
        }

        //make Repositories and Services Folders in app folder

        $directories = [
            app_path('Services'),
            app_path('Repositories'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->warn("Created directory: " . $directory);
            } else {
                $this->warn("Directory already exists: " . $directory);
            }
        }

        $this->info("Repositories and Services Folders created successfully.");

        // Generate Controller
        Artisan::call("make:controller {$name}Controller --resource");
        $this->warn("2. ");
        $this->info("Controller {$name} created successfully!");

        // Generate Request
        Artisan::call("make:request {$name}Request");
        $this->warn("3. ");
        $this->info("Request {$name} created successfully!");

        // Generate Resource
        Artisan::call("make:resource {$name}Resource");
        $this->warn("4. ");
        $this->info("Resource {$name} created successfully!");

        // Generate Seeder
        Artisan::call("make:seeder {$name}Seeder");
        $this->warn("5. ");
        $this->info("Seeder {$name} created successfully!");

        // Generate Factory
        Artisan::call("make:factory {$name}Factory");
        $this->warn("6. ");
        $this->info("Factory {$name} created successfully!");

        // Generate Route in api.php

        $filePath = base_path('routes/api.php');
        if (!File::exists($filePath)) {
            $this->error("api.php not found or route creation error !");
        } else {
            $routeCode = <<<EOD
        Route::group([
            'middleware' => ['auth:sanctum'],
            'prefix' => '$lowercase_name',
            'as' => '$lowercase_name'
        ], function () {
            Route::get("/$lowercase_name",[$nameController::class, 'index']);
            Route::post("/$lowercase_name",[$nameController::class, 'store']);
        });
        EOD;

            // Append the route code
            File::append($filePath, PHP_EOL . $routeCode);
            $this->warn("7. ");
            $this->info("Route for $name added successfully to api.php");
        }

        // Generate Repository
        $this->generateRepository($name);

        // Generate Service
        $this->generateService($name);
        $this->warn("......... ") . $this->info("Module {$name} created successfully!");
        $this->info("");
        $this->info("/////////////////////// done 😊 /////////////////////// ");
    }

    private function generateRepository($name)
    {
        $repositoryPath = app_path("Repositories/{$name}Repository.php");

        $path = app_path("Repositories/{$name}Repository.php");

        if (File::exists($path)) {
            $this->error("Repository already exists!");
            return;
        }

        File::ensureDirectoryExists(app_path('Repositories'));

        $stub = <<<PHP
        <?php

        namespace App\Repositories;
        use App\Models\\$name;

        class {$name}Repository
        {
             public function getAll()
             {
                 return $name::all();
             }

             public function getById(\$id)
             {
                 return $name::findOrFail(\$id);
             }

             public function getPaginate(\$perPage = 10)
             {
                 return $name::paginate(\$perPage);
             }

             public function create(array \$data)
             {
                 return $name::create(\$data);
             }

             public function update(\$id, array \$data)
             {
                 \$item = $name::findOrFail(\$id);
                 \$item->update(\$data);
                 return \$item;
             }

             public function delete(\$id)
             {
                 return $name::destroy(\$id);
             }
        }
        PHP;

        File::put($path, $stub);
        $this->warn("8. ");
        $this->info("Repository {$name} created successfully!");
    }

    private function generateService($name)
    {
        $servicePath = app_path("Services/{$name}Service.php");

        $path = app_path("Services/{$name}Service.php");

        if (File::exists($path)) {
            $this->error("Service already exists!");
            return;
        }

        File::ensureDirectoryExists(app_path('Services'));

        $stub = <<<PHP
        <?php

         namespace App\Services;

         use App\Repositories\\{$name}Repository;

         class {$name}Service
         {
             protected \$repository;

             public function __construct({$name}Repository \$repository)
             {
                 \$this->repository = \$repository;
             }

             public function getAll()
             {
                 return \$this->repository->getAll();
             }

             public function getById(\$id)
             {
                 return \$this->repository->getById(\$id);
             }

             public function getPaginate(\$perPage = 10)
             {
                 return \$this->repository->getPaginate(\$perPage);
             }

             public function create(array \$data)
             {
                 return \$this->repository->create(\$data);
             }

             public function update(\$id, array \$data)
             {
                 return \$this->repository->update(\$id, \$data);
             }

             public function delete(\$id)
             {
                 return \$this->repository->delete(\$id);
             }
         }

        PHP;

        File::put($path, $stub);
        $this->warn("9. ");
        $this->info("Service {$name} created successfully!");
    }
}
