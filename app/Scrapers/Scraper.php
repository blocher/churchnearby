<?php namespace App\Scrapers;

use Sunra\PhpSimple\HtmlDomParser;

abstract class Scraper {


	/**
	* Send a POST requst using cURL
	* @param string $url to request
	* @param array $post values to send
	* @param array $options for cURL
	* @return string
	*/
	protected function post($url, $fields = [], $options = [])
	{
		$hash = md5($url . serialize($fields) . serialize($options));
		//$result = $this->fromCache($hash);
		$result = null; // disabling cache
		if ($result !== null) {
			$this->log($url, 'POST (cache: '. $hash .')');
			return $result;
		} else {
			$this->log($url, 'POST');

			$defaults = [
				CURLOPT_POST => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_URL => $url,
				CURLOPT_FRESH_CONNECT => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FORBID_REUSE => 1,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_POSTFIELDS => http_build_query($fields),
				CURLOPT_HTTPHEADER => ['X-Requested-With: XMLHttpRequest']
			];

			$ch = curl_init();
			curl_setopt_array($ch, ($options + $defaults));
			
			$this->runCurl($ch);

			static::$requestsCount++;
			curl_close($ch);
			sleep(5);

			$this->saveToCache($hash, $result);
			return $result;
		}
	}

	/**
	* Send a GET requst using cURL
	* @param string $url to request
	* @param array $get values to send
	* @param array $options for cURL
	* @return string
	*/
	protected function get($url, $fields = [], $options = [])
	{
		$hash = md5($url . serialize($fields) . serialize($options));
		//$result = $this->fromCache($hash);
		$result = null; //forcing no cache
		if ($result !== null) {
			$this->log($url, 'GET (cache: '. $hash .')');
			return $result;
		} else {
			$this->log($url, 'GET');

			$defaults = [
				CURLOPT_URL => $url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($fields),
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_TIMEOUT => 30
			];

			$ch = curl_init();
			curl_setopt_array($ch, ($options + $defaults));

			$this->runCurl($ch);

			static::$requestsCount++;
			curl_close($ch);
			sleep(5);

			$this->saveToCache($hash, $result);
			return $result;
		}
	}

	private function runCurl($command, $retry = 3) {

		$result = curl_exec($command);	
		
		while (!$result && $retry !== 0) {
			// No valid response, sleep 10 min and retry.
			$this->log("No valid response, sleep 10 min and retry.");
			sleep(60*10);
			$result = curl_exec($command);
			$retry--;
		}

		if (!$result) {
			throw new \Exception('Failed after ' . static::$requestsCount . ' on ' . $url . PHP_EOL . print_r($fields, true) . PHP_EOL . curl_error($ch));
		}
		else {
			return $result;
		}
	}


	/**
	* Retreives an URL via FTP and saves it to the cache
	* @param string $url to request
	* @return string
	*/
	protected function getFTP($url)
	{
		$hash = md5($url);
		//$result = $this->fromCache($hash);
		$result = null; //forcing no cache
		if ($result !== null) {
			$this->log($url, 'FTP GET (cache: '. $hash .')');
			return $result;
		} else {
			$this->log($url, 'FTP GET');

			$result = @file_get_contents($url);
			if (!$result) {
				throw new \Exception('Failed after ' . static::$requestsCount . ' on ' . $url);
			}
			static::$requestsCount++;
			sleep(10);

			$this->saveToCache($hash, $result);
			return $result;
		}
	}

	/**
	 * Tries to load a URL from cache
	 * @param  string $hash   Hash to use as cache key
	 * @return string|null    Returns the contents or null if no cache entry exists
	 */
	private function fromCache($hash) {
		$cacheFile = __DIR__ . '/../../cache/' . $hash;

		if (file_exists($cacheFile)) {
			$modifiedTime = filemtime($cacheFile);
			$diff = time() - $modifiedTime;

			//Cache for up to 1 day
			if ($diff < (3600 * 24 * 1)) {
				return file_get_contents($cacheFile);
			}
		}

		return null;
	}

	/**
	 * Store contents in cache based on a hash
	 * @param  string $hash   Hash to use as cache key
	 * @param  string $contents File contents
	 */
	private function saveToCache($hash, $contents) {
		return; //we aren't doing cache
		$cacheFile = __DIR__ . '/../../cache/' . $hash;
		file_put_contents($cacheFile, $contents);
	}

	protected function log($text, $subject = null) {
		if ($subject) {
			echo chr(27) . '[32m' . $subject . chr(27) . '[0m: ' . $text;
		} else {
			echo $text;
		}

		echo PHP_EOL;
	}


}