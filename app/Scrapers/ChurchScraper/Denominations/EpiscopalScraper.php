<?php namespace App\Scrapers\ChurchScraper\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class EpiscopalScraper extends \App\Scrapers\ChurchScraper\ChurchScraper {


	private $url = 'http://www.episcopalchurch.org';
	private $directory = '/browse/parish';

	protected $denomination_slug = 'episcopal';
	protected $denomination;

	/* the main scrape function to kick it off*/
	public function scrape($startletter='A') {
		$letters = range($startletter,'Z');
		foreach ($letters as $letter) {
			$this->scrapeLetter($letter);
		}
	}

	public function resume() {
		
		$last_updated_church = \App\Models\Church::whereHas('region', function($q)
		{
		    $q->whereHas('denomination',function($q)
			{
				$q->where('slug',$this->denomination_slug);
			});
		})
		 ->orderBy('updated_at','desc')
		 ->first();
		
		 $last_updated_letter = strtoupper(substr($last_updated_church->name,0,1));
		 $this->scrape($last_updated_letter);
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
	private function scrapeChurch($church_url) {
		$url = $this->url . $church_url;
		$response = $this->get($url);
		$this->church_html  = HtmlDomParser::str_get_html($response);
		$this->saveChurch();
	}


	/* supports extractLatitude() and extractLongitude() -- not defined in abstract class*/
	private function extractLatitudeandLongitude() {
		foreach($this->church_html->find('.location a') as $item) {

			$latlng = [];
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
			//TODO: add comment
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
		$item = $this->church_html->find('article',0);
		return $item ? $this->clean(str_replace('node-','',$item->id)) : '';
	}

	public function extractLeader() {
		$item = $this->church_html->find('.field-name-field-clergy p',0);
		return $item ? $this->clean($item->innertext) : '';
	}

	public function extractLatitude() {
		$latln = $this->extractLatitudeandLongitude();
		return $latln['latitude'];
	}

	public function extractLongitude() {
		$latln = $this->extractLatitudeandLongitude();
		return $latln['longitude'];
	}

	public function extractName() {
		$item = $this->church_html->find('#page-title',0);
		return $item ? $this->clean($item->innertext) : '';
	}

	public function extractURL() {
		$item = $this->church_html->find('.field-name-field-website a',0);
		return $item ? $this->clean($item->href) : '';
	}

	public function extractAddress() {
		$item = $this->church_html->find('.vcard .street-address',0);
		return $item ? $this->clean($item->innertext) : '';
	}

	public function extractState() {
		$item = $this->church_html->find('.vcard .region',0);
		return $item ? $this->clean($item->innertext) : '';
	}

	public function extractCity() {
		$item = $this->church_html->find('.vcard .locality',0);
		return $item ? $this->clean($item->innertext) : '';
	}

	public function extractZip() {
		$item = $this->church_html->find('.vcard .postal-code',0);
		return $item ? $this->clean($item->innertext) : '';
	}

	public function extractEmail() {
		$item = $this->church_html->find('.field-name-field-email a',0);
		return $item ? $this->clean($item->innertext) : '';
	}

	public function extractPhone() {
		$item = $this->church_html->find('.field-name-field-phone',0);
		return $item ? $this->clean($this->parsePhone($item->innertext)) : '';
	}

	public function extractTwitter() {
		return '';
	}

	public function extractFacebook() {
		$item = $this->church_html->find('.field-name-field-facebook a',0);
		return $item ? $this->clean($item->href)  : '';
	}

	public function extractRegion() {

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
		$region->denomination_id = $this->denomination;
		$region->save();
		return $region->id;
	}



	protected function getDenominationSlug() {
		return 'episcopal';
	}
	
	protected function getDenominationName() {
		return 'The Episcopal Church';
	}
	
	protected function getDenominationUrl() {
		return 'http://www.episcopalchurch.org';

	}
	
	protected function getDenominationRegionName() {
		return 'Diocese';

	}
	
	protected function getDenominationRegionNamePlural() {
		return 'Dioceses';

	}
	
	protected function getDenominationTagName() {
		return 'Episcopal';
	}

	protected function getDenominationColor() {
		return 'Blue';

	}

}