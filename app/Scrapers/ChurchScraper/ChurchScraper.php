<?php namespace App\Scrapers\ChurchScraper;

use Sunra\PhpSimple\HtmlDomParser;


abstract class ChurchScraper extends \App\Scrapers\Scraper {

	protected $church_html = '';
	protected $denomination_slug = '';
	protected $denomination_id;

	function __construct() {
       $this->denomination_id =  $this->saveDenomination();
   	}

	protected function denominationID() {

		$id = \App\Models\Denomination::where('slug',$this->denomination_slug)
			->pluck('id');
		return $id;
	}

	protected function parsePhone($phone) {
		$phone = str_replace('<div  class="field-label field-label">Phone: </div>','',$phone);
		$phone = preg_replace("/[^0-9]/", '', $phone);
		$phone = '(' . substr($phone,0,3) . ') ' . substr($phone,3,3) . '-' . substr($phone,6,4);
		return $phone;
	}

	abstract function scrape();

	/* if resume functionality is available, overwrite in child class */
	public function resume() {
		echo  'No resume functionality is supported for this scraper. Starting from beginning.' . "\n";
		$this->scrape();
	}

	/*  Get the fields needed to save the church */
	abstract function extractExternalID();
	abstract function extractLeader();
	abstract function extractLatitude();
	abstract function extractLongitude();
	abstract function extractName();
	abstract function extractURL();
	abstract function extractAddress();
	abstract function extractState();
	abstract function extractCity();
	abstract function extractZip();
	abstract function extractEmail();
	abstract function extractPhone();
	abstract function extractTwitter();
	abstract function extractFacebook();
	abstract function extractRegion();

	/* save the church */
	public function saveChurch() {
		$id = $this->extractExternalID();
		$church = \App\Models\Church::firstOrNew(array('external_id' => $id));
		$church->external_id = $id;
		$church->leader = $this->extractLeader();
		$church->latitude = $this->extractLatitude();
		$church->longitude = $this->extractLongitude();
		$church->name = $this->extractName();
		$church->url = $this->extractURL();
		$church->address = $this->extractAddress();
		$church->state = $this->extractState();
		$church->city = $this->extractCity();
		$church->zip = $this->extractZip();
		$church->email = $this->extractEmail();
		$church->phone = $this->extractPhone();
		$church->twitter = $this->extractTwitter();
		$church->facebook = $this->extractFacebook();
		$church->region_id = $this->extractRegion();
		$church->save();
	}

	abstract protected function getDenominationSlug();
	abstract protected function getDenominationName();
	abstract protected function getDenominationUrl();
	abstract protected function getDenominationRegionName();
	abstract protected function getDenominationRegionNamePlural();
	abstract protected function getDenominationTagName();
	abstract protected function getDenominationColor();

	public function saveDenomination() {
		$slug = $this->getDenominationSlug();
		$denomination = \App\Models\Denomination::firstOrNew(array('slug' => $slug));
		$denomination->slug  = $this->getDenominationSlug();
		$denomination->name = $this->getDenominationName();
		$denomination->url = $this->getDenominationUrl();
		$denomination->region_name = $this->getDenominationRegionName();
		$denomination->region_name_plural = $this->getDenominationRegionNamePlural();
		$denomination->tag_name = $this->getDenominationTagName();
		$denomination->color = $region_name_plural = $this->getDenominationColor();
		$denomination->save();
		return $denomination->id;
	}

}