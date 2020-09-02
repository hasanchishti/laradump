<?php
namespace hasan\laradump;
use Illuminate\Support\ServiceProvider;

class DumpServiceProvider extends ServiceProvider{
	
	public function boot()
	{
		 if ($this->app->runningInConsole()) {
        $this->commands([
            Console\GeneratorCommand::class,
        ]);
    }
	}
	
	public function register()
	{
		
	}
}