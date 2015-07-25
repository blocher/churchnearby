<?php namespace App\Scrapers\ChurchScraper\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class CatholicScraper extends \App\Scrapers\ChurchScraper\ChurchScraper {


	private $url = 'http://www.thecatholicdirectory.com/';

	protected $denomination_slug = 'catholic';
	protected $denomination_id;

	private $current_diocese;
	private $current_church;
	private $current_id;
	private $current_map;

	/* the main scrape function to kick it off*/
	public function scrape($start_state='',$start_city='') {
		$start_city = str_replace(' ','%20',$start_city);
		$start_state = str_replace(' ','%20',$start_state);
		$states = $this->getStates();
		foreach ($states as $state) {
			$state_abbr = $this->extractParams($state)['state'];
			if (!empty($start_state) and $state_abbr!=$start_state) {
				continue;
			}
			$start_state = '';
			$cities = $this->getCities($state);
			foreach ($cities as $city) {
				$city_name = $this->extractParams($city)['absolutecity'];
				if (!empty($start_city) and $city_name!=$start_city) {
					continue;
				}
				$start_city = '';
				$parishes = $this->getParishes($city);
				foreach ($parishes as $parish) {
					$parish = $this->getParish($parish);
				}
			}
		}
	}


	public function resume() {
		$last_updated_church = \App\Models\Church::whereHas('region', function($q)
		{
		    $q->whereHas('denomination',function($q)
			{
				$q->where('id',$this->denomination_id);
			});
		})
		->where('city','!=','')
		->where('state','!=','')
		->orderBy('updated_at','desc')
		->first();

		 $state = $last_updated_church->state;
		 $city = $last_updated_church->city;
		 $this->scrape($state,$city);
		
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

	private function getDiocese() {


		$html = $this->church_html->find("a[href*=display_site_info]",0);
		if (!is_object($html)) {
			return '';
		}
		$long_name = $html->plaintext;
		$short_name = str_ireplace('Archdiocese of', '', $long_name);
		$short_name = str_ireplace('Diocese of', '', $short_name);
		$short_name = str_ireplace('Archdiocese of', '', $short_name);
		$short_name = str_ireplace('Eparchy of', '', $short_name);
		
		$slug = preg_replace("/[^A-Za-z0-9]/", '_', self::clean(strtolower($short_name)));

		$region = \App\Models\Region::firstOrNew(array('slug'=>$slug,'denomination_id'=>$this->denomination_id));

		$region->slug = $slug;
		$region->long_name = self::clean($long_name);
		$region->short_name = self::clean($short_name);
		$region->url = '';
		$region->denomination_id = $this->denomination_id;
		
		$region->save();
		$this->current_diocese = $region->id;
		return $region->id;

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

		$url = $this->church_html->find("a[href*=search_location&]",0);


		if (!is_object($url)) {

			$params['lat'] = '';
			$params['lng'] = '';
			$params['address']['street'] = '';
			$params['address']['city'] = '';
			$params['address']['state'] = '';
			$params['address']['zip'] = '';

		} else {
			$url = $url->href;
			$params = $this->extractParams($url);
			if (isset($params['addr'])) {
				$params['address'] = $this->extractMapAddress($params['addr']);
			}
		}

		$this->current_map  = $params;
		

	}


	private function extractParams($url) {
		
		$url = explode('?',$url)[1];
		$url = str_replace(' & ',' and ',$url);
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
		$address = array('street'=>[],'city'=>'','state'=>'','zip'=>'');
		while($component=array_pop($addr)) {
			if ($component == 'US') {
				continue;
			}
			if (empty($address['zip'] )&& preg_match('/^[0-9]{5}([- ]?[0-9]{4})?$/',$component)) {
				$address['zip'] = $component;
				continue;
			}

			if (empty($address['state']) && strlen($component)==2) {
				$address['state'] = $component;
				continue;
			}

			if (empty($address['city'])) {
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
		return isset($this->current_map['lat']) ? $this->current_map['lat'] : '';

	}

	public function extractLongitude() {
		return isset($this->current_map['lon']) ? $this->current_map['lon'] : '';
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
		return isset($this->current_map['address']['street']) ? $this->current_map['address']['street'] : '';
	}

	public function extractState() {
		return isset($this->current_map['address']['state']) ? $this->current_map['address']['state'] : '';
	}

	public function extractCity() {
		return isset($this->current_map['address']['city']) ? $this->current_map['address']['city'] : '';
	}

	public function extractZip() {
		return isset($this->current_map['address']['zip']) ? $this->current_map['address']['zip'] : '';
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
		return $this->getDiocese();
	}


	//Denomination stuff
	protected function getDenominationSlug() {
		return 'catholic';
	}
	
	protected function getDenominationName() {
		return 'The Roman Catholic Church';
	}
	
	protected function getDenominationUrl() {
		return 'http://w2.vatican.va/content/vatican/en.html';

	}
	
	protected function getDenominationRegionName() {
		return 'Dioceses';

	}
	
	protected function getDenominationRegionNamePlural() {
		return 'Diocese';
	}
	
	protected function getDenominationTagName() {
		return 'Catholic';
	}

	protected function getDenominationColor() {
		return 'Red';

	}

}