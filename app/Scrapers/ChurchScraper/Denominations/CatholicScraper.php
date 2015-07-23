<?php namespace App\Scrapers\ChurchScraper\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class CatholicScraper extends \App\Scrapers\ChurchScraper\ChurchScraper {


	private $url = 'http://www.thecatholicdirectory.com/';

	protected $denomination_slug = 'catholic';
	protected $denomination_id;

	private $current_synod;
	private $current_church;
	private $current_id;
	private $current_map;

	/* the main scrape function to kick it off*/
	public function scrape() {
		$states = $this->getStates();
		foreach ($states as $state) {
			$cities = $this->getCities($state);
			foreach ($cities as $city) {
				$parishes = $this->getParishes($city);
				foreach ($parishes as $parish) {
					$parish = $this->getParish($parish);
				}
			}
		}
	}



	/* step 1 */
	private function getStates() {


		$url = $this->url . 'index.cfm';
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);
		$items = $html->find('#_USA_Image_Map area');
		$states = [];
		foreach($items as $item) {
			$states[] = $item->href;
		}
		return $states;
	}

	/* step 2 */
	private function getCities($url) {

		if (empty($url)) {
			return;
		}

		$url = $this->url . $url;
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);
		$items = $html->find('#cities_row a');
		$cities = [];
		foreach($items as $item) {
			$cities[] = $item->href;
		}
		return $cities;
	}

	/* step 3 */
	private function getParishes($url) {

		if (empty($url)) {
			return;
		}

		$url = $this->url . $url . '&viewall=true&sort=alpha';
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);
		$items = $html->find('#cities_list .search_titledark a');
		$parishes = [];
		foreach($items as $item) {
			$parishes[] = $item->href;
		}
		return $parishes;
	}

	/* step 4 */
	private function getParish($url) {

		if (empty($url)) {
			return;
		}

		$url = $url . '&viewall=true&sort=alpha';
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);
		$this->church_html = $html;
		$this->setID($url);
		$this->setMapParams();
		return ($this->saveChurch());

	}




	private function setSynod($synod) {
		$short_name = substr($synod,5);
		$short_name = str_replace(' Synod, ELCA','',$short_name);
		$short_name = str_replace(', ELCA','',$short_name);
		$long_name = $short_name . ' ' . 'Synod';
		$slug = preg_replace("/[^A-Za-z0-9]/", '_', strtolower($short_name));
		$id = substr($synod,0,2);

		$region = \App\Models\Region::firstOrNew(array('slug' => $slug, 'denomination_id' => $this->denomination_id));
		$region->slug = $slug;
		$region->long_name = $long_name;
		$region->short_name = $short_name;
		$region->url = '';
		$region->denomination_id = $this->denomination_id;
		$region->save();
		$this->current_synod = $region->id;
		return $region->id;
	}

	public function resume() {
		
	
	}


	private function setID($url) {

		$url = explode('?',$url)[1];
		$parts = explode('&',$url);
		$parms = array();
		foreach ($parts as $part) {
			$part = explode('=',$part);
			$parms[$part[0]] = $part[1];
		}

		$this->current_id = '';
		if (isset($parms['siteid'])) {
			$this->current_id = $parms['siteid'];
		}

	}

	private function setMapParams() {

		$url = $this->church_html->find("a[href*=search_location&]",0)->href;
		$params = $this->extractMapParams($url);
		$params['address'] = $this->extractMapAddress($params['addr']);

		$this->current_map = '';
		$this->current_map  = $params;

	}


	private function extractMapParams($url) {
		
		$url = explode('?',$url)[1];
		$parts = explode('&',$url);
		$parms = array();
		foreach ($parts as $part) {
			$part = explode('=',$part);
			$parms[$part[0]] = $part[1];
		}

		return $parms;
	}

	private function extractMapAddress($addr) {

		$addr = explode(',',$addr);
		foreach ($addr as &$value) {
			$value = trim($value);
		}
		$address = array();
		while($component=array_pop($addr)) {
			if ($component == 'US') {
				continue;
			}
			if (!isset($address['zip'] )&& preg_match('/^[0-9]{5}([- ]?[0-9]{4})?$/',$component)) {
				$address['zip'] = $component;
				continue;
			}

			if (!isset($address['state']) && strlen($component)==2) {
				$address['state'] = $component;
				continue;
			}

			if (!isset($address['city'])) {
				$address['city'] = $component;
				continue;
			}

			$address['street'][] = $component;
		}
		$address['street'] = array_reverse($address['street']);
		$address['street'] = implode('; ',$address['street']);

		return $address;

	}


	/**
	*
	* These methods extract properties; implement abstract classes
	*
	*/

	public function extractExternalID() {
		return $this->current_id;
	}

	public function extractLeader() {

		$item = $this->church_html->find('span [itemprop=contactPoints]',0);
		if (is_object($item)) {
			return $item->plaintext;
		}
	}

	public function extractLatitude() {
		return $this->current_map['lat'];

	}

	public function extractLongitude() {
		return $this->current_map['lon'];
	}

	public function extractName() {
		return $this->church_html->find('.PageTitleListing',0)->plaintext;
	}

	public function extractURL() {
		$item = $this->church_html->find('a[itemprop=url]',0);
		if (is_object($item)) {
			return $item->plaintext;
		}
	}

	public function extractAddress() {
		return $this->current_map['address']['street'];
	}

	public function extractState() {
		return $this->current_map['address']['state'];
	}

	public function extractCity() {
		return $this->current_map['address']['city'];
	}

	public function extractZip() {
		return $this->current_map['address']['zip'];
	}

	public function extractEmail() {
		return '';
	}

	public function extractPhone() {
		$item = $this->church_html->find('span [itemprop=telephone]',0)->plaintext;
		$item = $this->parsePhone($item);
		return $item;
	}

	public function extractTwitter() {
		return '';
	}

	public function extractFacebook() {
		return '';
	}

	public function extractRegion() {
		//return $this->current_synod;
	}


	//Denomination stuff
	protected function getDenominationSlug() {
		return 'elca';
	}
	
	protected function getDenominationName() {
		return 'Evangelical Lutheran Church in America';
	}
	
	protected function getDenominationUrl() {
		return 'http://www.elca.org';

	}
	
	protected function getDenominationRegionName() {
		return 'Synods';

	}
	
	protected function getDenominationRegionNamePlural() {
		return 'Synods';
	}
	
	protected function getDenominationTagName() {
		return 'Lutheran (ELCA)';
	}

	protected function getDenominationColor() {
		return 'Green';

	}

}