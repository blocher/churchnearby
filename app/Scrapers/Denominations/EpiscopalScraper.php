<?php namespace App\Scrapers\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class EpiscopalScraper extends \App\Scrapers\Scraper {

	private $url = 'http://www.episcopalchurch.org/browse/parish';

	public function scrape() {
		$letters = range('A','Z');
		foreach ($letters as $letter) {
			$this->scrapeLetter($letter);
		}
	}

	private function scrapeLetter($letter) {

		$url = $this->url . '/' . $letter;
		$response = $this->get($url);
		$html = $html = HtmlDomParser::str_get_html($response);

		$last_page_link = $last_page_url_parts = $last_page_query_string = $last_page_query_parms = $last_page = '';
		foreach($html->find('ul.pagination li.last a') as $last_page_link) {
			$last_page_url_parts = parse_url($last_page_link->href);
			$last_page_query_string = htmlspecialchars_decode($last_page_url_parts['query']);
			parse_str($last_page_query_string, $last_page_query_parms);
			$last_page = $last_page_query_parms['page'];
			break;
		}	
		$last_page = empty($last_page) ? 0 : $last_page;

		for ($i=0; $i<=$last_page; $i++) {
			$this->scrapePage($letter, $i);
		}
	}

	private function scrapePage($letter,$page) {

		$url = $this->url . '/' . $letter . '&page=' . $page;
		echo $url."\n";

		//now let's get some churches then scrap call scrapeChurch

	}

	private function scrapeChurch() {

	}

}