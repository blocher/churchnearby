<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Scraper extends Command {


	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scrapes Parishes';

	 /**
     * The name and signature of the console command.
     *
     * @var string
     */
        protected $signature = 'scrape {denomination} {--resume}';

    /**


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

		$denomination = $this->argument('denomination');
		$class = '\App\Scrapers\ChurchScraper\Denominations\\'.$denomination.'Scraper';
		
		if (class_exists($class)) {
			$this->info ('Let\'s begin!');
		} else {
			$this->error ('The denomination you entered is not available.');
			return;
		}

		$scraper = new $class;

		if ($this->option('resume')) {
			$this->info('The scraper will attempt to resume from where it left off.');
			$scraper->resume();
		} else {
			$scraper->scrape();
		}
		
	}

}
