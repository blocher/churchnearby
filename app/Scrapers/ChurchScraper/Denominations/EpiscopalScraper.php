<?php namespace App\Scrapers\ChurchScraper\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class EpiscopalScraper extends \App\Scrapers\ChurchScraper\ChurchScraper {

	private $url = 'http://www.episcopalchurch.org';
	private $directory = '/browse/parish';

	protected $denomination_slug = 'episcopal';

	/* the main scrape function to kick it off*/
	public function scrape() {
		$letters = range('S','Z');
		foreach ($letters as $letter) {
			$this->scrapeLetter($letter);
		}
	}

	/*step 1*/
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

	/*step 2*/
	private function scrapePage($letter,$page) {

		$url = $this->url . $this->directory . '/' . $letter . '?page=' . $page;
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);
		foreach($html->find('td.views-field-title a') as $church) {
			$url = $church->href;
			$this->scrapeChurch($url);
		}
	}

	/*step 3 -- actually saves the church */ 
	private function scrapeChurch($url) {
		$url = $this->url . '/' . $url;
		$response = $this->get($url);
		$this->church_html  = HtmlDomParser::str_get_html($response);
		$this->saveChurch();
	}


	/* supports getLatitude() and getLongitude() -- not defined in abstract class*/
	private function getLatitudeandLongitude() {
		foreach($this->church_html->find('.location a') as $item) {

			$latlng = array();
			$latlng['latitude'] = $latlng['longitude'] = '';

			if (!isset($item->href)) {
				return $latlng;
			}

			$link = $item->href;

			if (empty($link)) {
				return $latlng;
			}

			$link = explode('=',$link);
			if (count($link)<=1) {
				return $latlng;
			}
			$link = $link[1];
			$link = explode('+',$link);
			if (count($link)<=1) {
				return $latlng;
			}
		
			
			foreach ($link as $part) {
				if (is_numeric($part) && $part>0) {
					$latlng['latitude'] = floatval($part);
				}
				if (is_numeric($part) && $part<0) {
					$latlng['longitude'] = floatval($part);
				}				
				if (!empty($latlng['latitude']) && $latlng['longitude']) {
					return $latlng;
				}
			}
			return $latlng;
		}
	}


	/**
	*
	* These methods get properties and implement abstract classes
	*
	*/

	public function getExternalID() {
		foreach($this->church_html->find('article') as $item) {
			return str_replace('node-','',$item->id);
		}
	}

	public function getLeader() {
		foreach($this->church_html->find('.field-name-field-clergy p') as $item) {
			return html_entity_decode(trim($item->innertext));
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
			return html_entity_decode(trim($item->innertext));
		}
	}

	public function getURL() {
		foreach($this->church_html->find('.field-name-field-website a') as $item) {
			return html_entity_decode(trim($item->href));
		}
	}

	public function getAddress() {
		foreach($this->church_html->find('.vcard .street-address') as $item) {
			return html_entity_decode(trim($item->innertext));
		}
	}

	public function getState() {
		foreach($this->church_html->find('.vcard .region') as $item) {
			return html_entity_decode(trim($item->innertext));
		}
	}

	public function getCity() {
		foreach($this->church_html->find('.vcard .locality') as $item) {
			return html_entity_decode(trim($item->innertext));
		}
	}

	public function getZip() {
		foreach($this->church_html->find('.vcard .postal-code') as $item) {
			return html_entity_decode(trim($item->innertext));
		}
	}

	public function getEmail() {
		foreach($this->church_html->find('.field-name-field-email a') as $item) {
			return html_entity_decode(trim($item->innertext));
		}
	}

	public function getPhone() {
		foreach($this->church_html->find('.field-name-field-phone a') as $item) {
			return html_entity_decode(trim($item->innertext));
		}
	}

	public function getTwitter() {
		return '';
	}

	public function getFacebook() {
		foreach($this->church_html->find('.field-name-field-facebook a') as $item) {
			return html_entity_decode(trim($item->href));
		}
	}

	public function getRegion() {

		$link = '';
		foreach($this->church_html->find('.field-name-field-er-diocese a') as $item) {
			$link = trim($item->href);
			$name = trim($item->innertext);
			break;
		}

		if (empty($link)) {
			$slug = 'unknown';
			$name = 'Unknown';
		} else {
			$link = explode("/",$link);
			$slug = $link[2];
		}

		$region = \App\Models\Region::firstOrNew(array('slug' => $slug));
		$region->slug = $slug;
		$region->long_name = 'Diocese of '.$name;
		$region->short_name = $name;
		$region->url = '';
		$region->denomination = $this->denominationID();
		$region->save();
		return $region->id;
	}



}