<?php namespace App\Scrapers\ChurchScraper\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class ElcaScraper extends \App\Scrapers\ChurchScraper\ChurchScraper {


	private $url = 'http://www.elca.org';
	private $directory = '';

	protected $denomination_slug = 'elca';

	/* the main scrape function to kick it off*/
	public function scrape() {
		echo 'begin';
		echo 'end';
		
	}

	public function resume() {
		
	
	}

	/**
	*
	* Helpers to clean results
	*
	*/

	private static function clean($string) {
		return html_entity_decode(trim($string));
	}


	/**
	*
	* These methods extract properties; implement abstract classes
	*
	*/

	public function extractExternalID() {
		
	}

	public function extractLeader() {
	}

	public function extractLatitude() {

	}

	public function extractLongitude() {
	}

	public function extractName() {

	}

	public function extractURL() {
	}

	public function extractAddress() {
	}

	public function extractState() {
	}

	public function extractCity() {
	}

	public function extractZip() {

	}

	public function extractEmail() {
	}

	public function extractPhone() {
	}

	public function extractTwitter() {
	}

	public function extractFacebook() {
	}

	public function extractRegion() {
	}

}