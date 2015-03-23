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
	abstract function getExternalID();
	abstract function getLeader();
	abstract function getLatitude();
	abstract function getLongitude();
	abstract function getName();
	abstract function getURL();
	abstract function getAddress();
	abstract function getState();
	abstract function getCity();
	abstract function getZip();
	abstract function getEmail();
	abstract function getPhone();
	abstract function getTwitter();
	abstract function getFacebook();
	abstract function getRegion();

	/* save the church */
	public function saveChurch() {
		$id = $this->getExternalID();
		$church = \App\Models\Church::firstOrNew(array('externalid' => $id));
		$church->externalid = $id;
		$church->leader = $this->getLeader();
		$church->latitude = $this->getLatitude();
		$church->longitude = $this->getLongitude();
		$church->name = $this->getName();
		$church->url = $this->getURL();
		$church->address = $this->getAddress();
		$church->state = $this->getState();
		$church->city = $this->getCity();
		$church->zip = $this->getZip();
		$church->email = $this->getEmail();
		$church->phone = $this->getPhone();
		$church->twitter = $this->getTwitter();
		$church->facebook = $this->getFacebook();
		$church->region = $this->getRegion();
		$church->save();
	}

}