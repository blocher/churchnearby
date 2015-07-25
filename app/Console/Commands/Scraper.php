<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Scraper extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'scrape';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scrapes Parishes';

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
	public function fire()
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

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
            array('denomination', InputArgument::REQUIRED, 'The denomination you wish to scrape'),
        );
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('resume', 'r' , InputOption::VALUE_NONE, 'Resume from where the scraper last left off')
		);
	}

}
