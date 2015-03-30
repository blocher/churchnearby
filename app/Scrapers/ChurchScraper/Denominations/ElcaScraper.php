<?php namespace App\Scrapers\ChurchScraper\Denominations;

use Sunra\PhpSimple\HtmlDomParser;

class ElcaScraper extends \App\Scrapers\ChurchScraper\ChurchScraper {


	private $url = 'http://www.elca.org';
	private $directory = '/tools/FindACongregation';

	protected $denomination_slug = 'elca';

	private $current_synod;
	private $church_json;

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
			continue;

			$url = 'http://search.elca.org/_layouts/15/ELCA.Search/Handlers/MapHandler.ashx';
			$fields = [
				'type' => 'Synod',
				'synodName' => $synod,
				'language' => '',
			];

			$response = $this->post($url,$fields);
			$response = json_decode($response);



		}

	}

	private function setSynod($synod) {
		
		
		$short_name = substr($synod,5);
		$short_name = str_replace(' Synod, ELCA','',$short_name);
		$long_name = $short_name . ' ' . 'Synod';
		$slug = preg_replace("/[^A-Za-z0-9]/", '_', strtolower($short_name));
		$id = substr($synod,0,2);

		$region = \App\Models\Region::firstOrNew(array('slug' => $slug));
		$region->slug = $slug;
		$region->long_name = $long_name;
		$region->short_name = $short_name;
		$region->url = '';
		$region->denomination = $this->denominationID();
		$region->save();
		$this->current_synod = $region->id;
		return $region->id;
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