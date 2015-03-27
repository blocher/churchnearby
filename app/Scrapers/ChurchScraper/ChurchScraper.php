<?php namespace App\Scrapers\ChurchScraper;

use Sunra\PhpSimple\HtmlDomParser;


abstract class ChurchScraper extends \App\Scrapers\Scraper {

	protected $church_html = '';
	protected $denomination_slug = '';

	public function denominationID() {

		$id = \App\Models\Denomination::where('slug',$this->denomination_slug)
			->pluck('id');
		return $id;
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
		$church = \App\Models\Church::firstOrNew(array('externalid' => $id));
		$church->externalid = $id;
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
		$church->region = $this->extractRegion();
		$church->save();
	}

}