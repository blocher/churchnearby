<?php namespace App\Scrapers\ChurchScraper\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class ElcaScraper extends \App\Scrapers\ChurchScraper\ChurchScraper {


	private $url = 'http://www.elca.org';
	private $directory = '/tools/FindACongregation';

	protected $denomination_slug = 'elca';
	protected $denomination_id;

	private $current_synod;
	private $current_church;

	/* the main scrape function to kick it off*/
	public function scrape() {
		$synods = $this->getSynods();
		$this->scrapeSynods($synods);
		
	}

	/* step 1 */
	private function getSynods() {

		$url = $this->url . $this->directory;
		$response = $this->get($url);
		$html  = HtmlDomParser::str_get_html($response);
		$item = $html->find('#synod-search-synod option');
		$synods = [];
		foreach ($item as $item) {
			$text = $item->innertext;
			if (!empty($text)) {
				$synods[] = $item->innertext;
			}
		}
		return $synods;

	}

	/* step 2 */
	private function scrapeSynods($synods) {

		if (empty($synods) || !is_array($synods)) {
			return;
		}

		foreach ($synods as $synod) {

			$this->setSynod($synod);


			$url = 'http://search.elca.org/_layouts/15/ELCA.Search/Handlers/MapHandler.ashx';
			$fields = [
				'type' => 'Synod',
				'synodName' => $synod,
				'language' => '',
			];

			$response = $this->post($url,$fields);
			$response = json_decode($response);

			echo $synod . "\n";

			foreach ($response as $church) {
				$this->current_church = $church;
				$this->saveChurch();
			}

			$this->saveChurch();

		}

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


	/**
	*
	* These methods extract properties; implement abstract classes
	*
	*/

	public function extractExternalID() {
		return $this->current_church->LocationID;
	}

	public function extractLeader() {
		return '';
	}

	public function extractLatitude() {
		return $this->current_church->Latitude;

	}

	public function extractLongitude() {
		return $this->current_church->Longitude;
	}

	public function extractName() {
		return $this->current_church->LocationName;

	}

	public function extractURL() {
		return "http://search.elca.org/Pages/Location.aspx?LocationID=" . $this->current_church->LocationID . "&LocationType=" . $this->current_church->LocationType;
	}

	public function extractAddress() {
		return $this->current_church->LocationStreetAddress;
	}

	public function extractState() {
		return $this->current_church->LocationState;
	}

	public function extractCity() {
		return $this->current_church->LocationCity;
	}

	public function extractZip() {
		return $this->current_church->LocationZIP;
	}

	public function extractEmail() {
		return '';
	}

	public function extractPhone() {
		return '';
	}

	public function extractTwitter() {
		return '';
	}

	public function extractFacebook() {
		return '';
	}

	public function extractRegion() {
		return $this->current_synod;
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