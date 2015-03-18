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
		$class = '\App\Scrapers\Denominations\\'.$denomination.'Scraper';
		
		if (class_exists('\App\Scrapers\Denominations\\'.$denomination.'Scraper')) {
			$this->info ('Let\'s scrape');
		} else {
			$this->error ('The denomination you entered is not available.');
		}

		$scraper = new $class;

		$scraper->test();
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
		);
	}

}
