<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   1.1.3
 * @link      https://lazydev.pro
 */
 
if (!defined('DATALIFEENGINE')) {
	die('Hacking attempt!');
}

class dleMoonwalk
{
	private static $instance = null;
	private static $dle_config = [], $moonwalk_config = [], $moonwalk_lang = [];
	private static $cache_type = '', $cache_id = '';
	private static $api = [
		'db' => [
			'moonwalk' => 'http://dlemoon.online/api'
		],
		'field' => [
			'title' => 'title',
			'id_kinopoisk' => 'kinopoisk_id',
			'id_worldart' => 'world_art_id'
		]
	];
	private static $json;
	
	private function __construct() {}
	private function __wakeup() {}
	private function __clone() {}
	private function __sleep() {}
	
	public static function realize()
	{
        if (null === self::$instance) {
            self::$instance = new self();
        }
		
        return self::$instance;
	}
	
	public static function config($config, $dle_moonwalk_config, $dle_moonwalk_lang)
	{
		self::$dle_config = $config;
		self::$moonwalk_config = $dle_moonwalk_config;
		self::$moonwalk_lang = $dle_moonwalk_lang;
		
		return self::$instance;
	}
	
	public static function parseAdmin($fieldChoose, $chooseDb, $searchData)
	{
		self::$cache_type = self::$api['db'][$chooseDb];
		self::$cache_id = urlencode($searchData);
		$j = self::getCache();
		if ($j == false) {
			$url = self::$api['db'][$chooseDb] . '/videos.json?api_token=' . self::$moonwalk_config['main']['api_token'] . '&' .self::$api['field'][$fieldChoose] . '=' . urlencode($searchData);
			self::$json = self::curl($url);
		} else {
			self::$json = $j;
		}
		
		return self::$instance;
	}
	
	public static function parseCron($fieldChoose, $chooseDb, $searchData)
	{
		$url = self::$api['db'][$chooseDb] . '/videos.json?api_token=' . self::$moonwalk_config['main']['api_token'] . '&' .self::$api['field'][$fieldChoose] . '=' . urlencode($searchData);
		return self::curl($url);
	}
	
	public static function templateAdmin()
	{
		$jsonDecode = json_decode(self::$json, true);
		if ($jsonDecode['error'] != '') {
			if ($jsonDecode['error'] == 'videos_not_found') {
				return json_encode(['error' => self::$moonwalk_lang[96]]);
			}
			return self::$json;
		} else {
			$template = file_get_contents(ENGINE_DIR . '/dle_moonwalk/inc/tpl/result.tpl');
			$templateArraySerial = [];
			$templateArrayFilm = [];
			
			foreach ($jsonDecode as $key => $array) {
				if (self::$moonwalk_config['main']['voice']) {
					if (in_array($array['translator_id'], self::$moonwalk_config['main']['voice'])) {
						continue;
					}
				}
				
				$type = $array['type'] == 'movie' ? self::$moonwalk_lang[97] : self::$moonwalk_lang[98];
				$title = '';
				if ($array['title_ru'] != '') {
					$title = stripslashes($array['title_ru']);
				}
				if ($array['title_en'] != '') {
					 $title = $title != '' ? $title . ' | ' . stripslashes($array['title_en']) : stripslashes($array['title_en']);
				}
				$adv = $array['instream_ads'] == 1 ? '\\1' : '';
				$quality = $array['camrip'] ? 'CAMRip' : 'HDRip';
				if ($array['source_type']) {
					$quality = $array['source_type'];
				}
				$voice = $array['translator'] ? $array['translator'] : 'n/a';
				$typeInt = $array['type'] == 'movie' ? 0 : 1;
				
				$temp = $template;
				
				$temp = str_replace('{year}', $array['year'], $temp);
				$temp = str_replace('{type}', $type, $temp);
				$temp = str_replace('{title}', $title, $temp);
				$temp = preg_replace("#\[adv\](.*?)\[/adv\]#is", $adv, $temp);
				$translator = [];
				$translator[$array['token']][$array['translator_id']] = $array['translator'];
				if ($typeInt == 1) {
					$episodes = [];
					array_walk($array['season_episodes_count'], function($item) use(&$episodes, $array) {
						$episodes[$array['token']][$item['season_number']] = implode(',', $item['episodes']);
					});
					$temp = str_replace('{episodes}', htmlentities(json_encode($episodes)), $temp);
					$temp = preg_replace("#\[serial\](.*?)\[/serial\]#is", '\\1', $temp);
					$temp = preg_replace("#\[serial\](.*?)\[/serial\]#is", '\\1', $temp);
					$temp = preg_replace("#\[movie\](.*?)\[/movie\]#is", '', $temp);
				} else {
					$temp = preg_replace("#\[movie\](.*?)\[/movie\]#is", '\\1', $temp);
					$temp = preg_replace("#\[serial\](.*?)\[/serial\]#is", '', $temp);
				}
				$temp = str_replace('{quality}', $quality, $temp);
				$temp = str_replace('{voice}', $voice, $temp);
				$temp = str_replace('{url}', $array['iframe_url'], $temp);
				$temp = str_replace('{type-video}', $array['type'] == 'movie' ? 'movie' : 'serial', $temp);
				$temp = str_replace('{token}', $array['token'], $temp);
				if ($typeInt == 1) {
					$templateArraySerial[$array['year']] .= $temp;
				} else {
					$templateArrayFilm[$array['year']] .= $temp;
				}
			}
			
			$result = ['content' => '', 'error' => ''];
			if ($templateArrayFilm) {
				krsort($templateArrayFilm);
				$result['content'] .= implode($templateArrayFilm);
			}
			
			if ($templateArraySerial) {
				krsort($templateArraySerial);
				$result['content'] .= implode($templateArraySerial);
			}
			
			if (!$result['content']) {
				$result['error'] = self::$moonwalk_lang[96];
			}
			
			$result = json_encode($result);
			self::setCache(self::$json);
			return $result;
		}
	}
	
	public static function setCache($data)
	{
		self::$cache_id = md5(self::$cache_type . '_' . self::$cache_id);
		file_put_contents(ENGINE_DIR . '/dle_moonwalk/cache/' . self::$cache_id . '.tmp', $data, LOCK_EX);
		@chmod(ENGINE_DIR . '/dle_moonwalk/cache/' . self::$cache_id . '.tmp', 0666);
	}
	
	public static function getCache()
	{
		self::$cache_id = md5(self::$cache_type . '_' . self::$cache_id);
		$file = ENGINE_DIR . '/dle_moonwalk/cache/' . self::$cache_id . '.tmp';
		$buffer = false;
		if (file_exists($file)) {
			$buffer = file_get_contents($file);

			$file_date = filemtime($file);
			$file_date = time() - $file_date;

			if ($file_date > (180 * 60)) {
				$buffer = false;
				@unlink($file);
			}
		}
		return $buffer;
	}
	
	public static function setData($type, $token)
	{
		$url = self::$api['db']['moonwalk'] . '/' . $type . '.json?api_token=' . self::$moonwalk_config['main']['api_token'] . '&token=' . urlencode($token);
		$json = self::curl($url);
		$json = json_decode($json, true);
		
		$dataArray = [];
		$fieldData = self::$moonwalk_config['data'];
		
		if (self::$moonwalk_config[$type]['change_meta'] == 1 && self::$moonwalk_config[$type]['meta_title'] != '') {
			$fieldData['meta_title'] = self::$moonwalk_config[$type]['meta_title'];
		}
		
		$keys = ['title_ru', 'title_en', 'translator', 'year', 'description', 'countries', 'actors', 'genres', 'directors', 'age', 'kinopoisk_rating', 'kinopoisk_votes', 'imdb_rating', 'imdb_votes', 'iframe_url', 'trailer_iframe_url', 'kinopoisk_id', 'world_art_id', 'duration', 'quality'];
		foreach ($keys as $key) {
			if ($json[$key]) {
				$dataArray[$key] = stripslashes($json[$key]);
			} elseif ($json['material_data'][$key] && is_array($json['material_data'][$key])) {
				$dataArray[$key] = stripslashes(implode(', ', $json['material_data'][$key]));
			} elseif ($json['material_data'][$key]) {
				$dataArray[$key] = stripslashes($json['material_data'][$key]);
			}
		}
		
		if (isset($json['duration']['human'])) {
			$dataArray['duration'] = $json['duration']['human'];
		}

		$dataArray['quality'] = $json['camrip'] ? 'CAMRip' : 'HDRip';
		if ($json['source_type']) {
			$dataArray['quality'] = $json['source_type'];
		}
		
		if ($type == 'serial') {
			$season = end($json['season_episodes_count']);
			$seria = $season['episodes_count'];
			$season = $season['season_number'];
		}
		
		foreach ($fieldData as $field => $data) {
			$tempData = $data;
			foreach ($keys as $key) {
				if ($key == 'iframe_url') {
					$tempData = preg_replace('#\[tag-video\](.*?)\[\/tag-video\]#is', ($dataArray[$key] != '' ? '$1' : ''), $tempData);
					if (self::$moonwalk_config['main']['ssl']) {
						$dataArray[$key] = preg_replace("#http(s)?://[^/]+/#iu", 'https://streamguard.cc/', $dataArray[$key]);
					} elseif (self::$moonwalk_config['main']['domain'] != '') {
						$dataArray[$key] = preg_replace("#http(s)?://[^/]+/#iu", 'http://' . self::$moonwalk_config['main']['domain'] . '/', $dataArray[$key]);
					}
					$tempData = str_replace('{video}', $dataArray[$key], $tempData);
				} elseif($key == 'trailer_iframe_url') {
					$tempData = preg_replace('#\[tag-trailer\](.*?)\[\/tag-trailer\]#is', ($dataArray[$key] != '' ? '$1' : ''), $tempData);
					$tempData = str_replace('{trailer}', $dataArray[$key], $tempData);
				} else {
					if ($key == 'title_ru' || $key == 'title_en') {
						$tempData = preg_replace('#\[tag-not-' . $key . '\](.*?)\[\/tag-not-' . $key . '\]#is', ($dataArray[$key] != '' ? '' : '$1'), $tempData);
					}
					$tempData = preg_replace('#\[tag-' . $key . '\](.*?)\[\/tag-' . $key . '\]#is', ($dataArray[$key] != '' ? '$1' : ''), $tempData);
					$tempData = str_replace('{' . $key . '}', $dataArray[$key], $tempData);
				}
			}
			
			if ($type == 'serial') {
				if (strpos($tempData, '{season}') !== false) {
					$tempData = str_replace('{season}', $season, $tempData);
					$tempData = preg_replace('#\[tag-season\](.*?)\[\/tag-season\]#is', '$1', $tempData);
				}
				
				if (strpos($tempData, '{seria}') !== false) {
					$tempData = str_replace('{seria}', $seria, $tempData);
					$tempData = preg_replace('#\[tag-seria\](.*?)\[\/tag-seria\]#is', '$1', $tempData);
				}
				
				if (strpos($tempData, '{season-format-') !== false) {
					preg_match('#{season-format-([0-9]+)}#is', $tempData, $tagSeasonFormat);
					if ($tagSeasonFormat[1]) {
						$tempData = str_replace('{season-format-' . $tagSeasonFormat[1] . '}', self::switchType($season, $tagSeasonFormat[1], 'season'), $tempData);
						$tempData = preg_replace('#\[tag-season-format-' . $tagSeasonFormat[1] . '\](.*?)\[\/tag-season-format-' . $tagSeasonFormat[1] . '\]#is', '$1', $tempData);
					} else {
						$tempData = preg_replace('#\[tag-season-format-([0-9]+)\](.*?)\[\/tag-season-format-([0-9]+)\]#is', '', $tempData);
						$tempData = preg_replace('#{season-format-([0-9]+)}#is', '', $tempData);
					}
				}
				
				if (strpos($tempData, '{seria-format-') !== false) {
					preg_match('#{seria-format-([0-9]+)}#is', $tempData, $tagSeriaFormat);
					if ($tagSeriaFormat[1]) {
						$tempData = str_replace('{seria-format-' . $tagSeriaFormat[1] . '}', self::switchType($seria, $tagSeriaFormat[1], 'seria'), $tempData);
						$tempData = preg_replace('#\[tag-seria-format-' . $tagSeriaFormat[1] . '\](.*?)\[\/tag-seria-format-' . $tagSeriaFormat[1] . '\]#is', '$1', $tempData);
					} else {
						$tempData = preg_replace('#\[tag-seria-format-([0-9]+)\](.*?)\[\/tag-seria-format-([0-9]+)\]#is', '', $tempData);
						$tempData = preg_replace('#{seria-format-([0-9]+)}#is', '', $tempData);
					}
				}
			} else {
				$tempData = preg_replace('#\[tag-season\](.*?)\[\/tag-season\]#is', '', $tempData);
				$tempData = preg_replace('#\[tag-seria\](.*?)\[\/tag-seria\]#is', '', $tempData);
				$tempData = preg_replace('#\[tag-season-format-([0-9]+)\](.*?)\[\/tag-season-format-([0-9]+)\]#is', '', $tempData);
				$tempData = preg_replace('#\[tag-seria-format-([0-9]+)\](.*?)\[\/tag-seria-format-([0-9]+)\]#is', '', $tempData);
			}
			
			if (strpos($tempData, '[tag-') !== false) {
				$tempData = preg_replace('#\[tag-(.+?)\](.*?)\[\/tag-\\1\]#is', '', $tempData);
			}
			if (strpos($tempData, '{') !== false) {
				$tempData = preg_replace('#{(.+?)}#is', '', $tempData);
			}

			$tempData = preg_replace("/[\r\n]+/", "\n", $tempData);
			if (self::$dle_config['allow_admin_wysiwyg'] == 1 || self::$dle_config['allow_admin_wysiwyg'] == 2) {
				$tempData = str_replace("\n", "<br />", $tempData);
			}
			
			$fieldData[$field] = $tempData;
		}
		
		$dataJson = ['api' => $fieldData, 'config' => ['editor' => self::$dle_config['allow_admin_wysiwyg']]];
		return json_encode($dataJson);
	}
	
	
	public static function switchType($data, $cfg, $type)
	{
		$type = $type == 'seria' ? self::$moonwalk_lang[99] : self::$moonwalk_lang[100];
		if ($data == 1) {
			$rerunData = $data . $type;
		} else {
			switch($cfg) {
				case 1:
					$rerunData = $data . $type;
					break;
				case 2:
					$rerunData = '1-' . $data . $type;
					break;
				case 3:
					$rerunData = implode(',', range(1, $data)) . $type;
					break;
				case 4:
					if ($data > 3) {
						$rerunData = '1-' . ($data - 2) . ',' . (implode(',', range($data - 1, $data))) . $type;
					} else {
						$rerunData = '1-' . $data . $type;
					}
					break;
			}
		}
		
		return $rerunData;
	}
	
	public static function curl($url)
	{
		if ($ch = curl_init($url)) {
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: ' . self::$dle_config['http_home_url']]);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$response = curl_exec($ch);
			curl_close($ch);

			if ($response === false) {
				$response = json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
			}
		} else {
			$response = json_encode(['error' => 'Curl is not installed in your PHP installation']);
		}
		return $response;
	}
}