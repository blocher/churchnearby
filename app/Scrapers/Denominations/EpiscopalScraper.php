<?php namespace App\Scrapers\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class EpiscopalScraper extends \App\Scrapers\Scraper {

	private $url = 'http://www.episcopalchurch.org';
	private $directory = '/browse/parish';

	public function scrape() {
		$letters = range('A','Z');
		foreach ($letters as $letter) {
			$this->scrapeLetter($letter);
		}
	}

	private function scrapeLetter($letter) {

		$url = $this->url . $this->directory . '/' . $letter;
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);

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

		$url = $this->url . $this->directory . '/' . $letter . '?page=' . $page;
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);
		foreach($html->find('td.views-field-title a') as $church) {
			$url = $church->href;
			$this->scrapeChurch($url);
		}
	}

	private function getLatitudeandLongitude() {
		foreach($this->church_html->find('.location a') as $item) {

			$link = $item->href;
			$link = explode('=',$link);
			$link = $link[1];
			$link = explode('+',$link);
		
			$latlng = array();
			$latlng['latitude'] = $latlng['longitude'] = '';
			foreach ($link as $part) {
				if (is_numeric($part) && $part>0) {
					$latlng['latitude'] = $part;
				}
				if (is_numeric($part) && $part<0) {
					$latlng['longitude'] = $part;
				}				
				if (!empty($latlng['latitude']) && $latlng['longitude']) {
					return $latlng;
				}
			}
			return $latlng;
		}
	}

	public function getExternalID() {
		foreach($this->church_html->find('article') as $item) {
			return str_replace('node-','',$item->id);
		}
	}

	public function getLeader() {
		foreach($this->church_html->find('.field-name-field-clergy p') as $item) {
			return trim($item->innertext);
		}
	}

	public function getLatitude() {
		$latln = $this->getLatitudeandLongitude();
		return $latln['latitude'];
	}

	public function getLongitude() {
		$latln = $this->getLatitudeandLongitude();
		return $latln['longitude'];
	}

	public function getName() {
		foreach($this->church_html->find('#page-title') as $item) {
			return trim($item->innertext);
		}
	}

	public function getURL() {
		foreach($this->church_html->find('.field-name-field-website a') as $item) {
			return trim($item->href);
		}
	}

	public function getAddress() {
		foreach($this->church_html->find('.vcard .street-address') as $item) {
			return trim($item->innertext);
		}
	}

	public function getState() {
		foreach($this->church_html->find('.vcard .region') as $item) {
			return trim($item->innertext);
		}
	}

	public function getCity() {
		foreach($this->church_html->find('.vcard .locality') as $item) {
			return trim($item->innertext);
		}
	}

	public function getZip() {
		foreach($this->church_html->find('.vcard .postal-code') as $item) {
			return trim($item->innertext);
		}
	}

	public function getEmail() {
		foreach($this->church_html->find('.field-name-field-email a') as $item) {
			return trim($item->innertext);
		}
	}

	public function getPhone() {
		foreach($this->church_html->find('.field-name-field-phone a') as $item) {
			return trim($item->innertext);
		}
	}

	public function getTwitter() {
		return '';
	}

	public function getFacebook() {
		foreach($this->church_html->find('.field-name-field-facebook a') as $item) {
			return trim($item->href);
		}
	}

	private function scrapeChurch($url) {

		$url = $this->url . '/' . $url;
		$response = $this->get($url);
		$this->church_html  = HtmlDomParser::str_get_html($response);

		$this->saveChurch();

	}

}