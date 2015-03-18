<?php namespace App\Scrapers\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class EpiscopalScraper extends \App\Scrapers\Scraper {

	private $url = 'http://www.episcopalchurch.org/browse/parish';

	public function scrape() {
		echo $this->get($this->url);
	}

}