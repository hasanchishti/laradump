<?php
namespace hasanchishti\laradump;
namespace hasanchishti\laradump\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generator {name : Class (singular), e.g User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
	
	protected function getStub($type)
	{
    	return file_get_contents(realpath(dirname(__FILE__)."/../stubs/$type.stub"));
	}
	
	protected function model($name)
	{
		$modelTemplate = str_replace(
			['{{modelName}}'],
			[$name],
			$this->getStub('Model')
		);
	
		file_put_contents(app_path("/{$name}.php"), $modelTemplate);
	}

	protected function controller($name)
	{
		$controllerTemplate = str_replace(
			[
				'{{modelName}}',
				'{{modelNamePluralLowerCase}}',
				'{{modelNameSingularLowerCase}}'
			],
			[
				$name,
				strtolower(Str::plural($name)),
				strtolower($name)
			],
			$this->getStub('Controller')
		);
	
		file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
	}
	
	protected function request($name)
	{
		$requestTemplate = str_replace(
			['{{modelName}}'],
			[$name],
			$this->getStub('Request')
		);
	
		if(!file_exists($path = app_path('/Http/Requests')))
			mkdir($path, 0777, true);
	
		file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $requestTemplate);
	}
	
	protected function views($name)
	{
		$listTemplate = $this->getStub('lists');
		$createTemplate = $this->getStub('create');
		$showTemplate = $this->getStub('show');
		$name = strtolower(Str::plural($name));
		if(!file_exists($path = base_path("resources/views/{$name}")))
			mkdir($path, 0777, true);
			
		file_put_contents(base_path("resources/views/{$name}/list.blade.php"), $listTemplate);
		file_put_contents(base_path("resources/views/{$name}/create.blade.php"), $createTemplate);
		file_put_contents(base_path("resources/views/{$name}/show.blade.php"), $showTemplate);
	}
	
	protected function routes($name)
	{
		
		file_put_contents(base_path("routes/web.php"),'Route::resource(\'' . Str::plural(strtolower($name)) . "', '{$name}Controller');\n",FILE_APPEND);
		
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

		$this->controller($name);
		$this->model($name);
		$this->request($name);
		$this->views($name);
		$this->routes($name);
		//Storage::disk('routes')->append('web.php', 'Route::resource(\'' . Str::plural(strtolower($name)) . "', '{$name}Controller');");
		$this->info("Crud generated successfully");
    }
}
