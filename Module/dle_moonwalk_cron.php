<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   1.1.2
 * @link      https://lazydev.pro
 */
 
@set_time_limit(0);

@error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('AUTOMODE', true);
define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';
if ($_GET['token'] != $dle_moonwalk_config['main']['api_token']) {
	exit;
}

include ENGINE_DIR . '/dle_moonwalk/helpers/dle_moonwalk.php';
include ENGINE_DIR . '/dle_moonwalk/language/dle_moonwalk_admin_lang.lng';
include ENGINE_DIR . '/data/config.php';

date_default_timezone_set($config['date_adjust']);

if (file_exists(ENGINE_DIR . '/classes/plugins.class.php')) {
	require_once (ENGINE_DIR . '/classes/plugins.class.php');
	require_once (DLEPlugins::Check(ENGINE_DIR.'/modules/functions.php'));
} else {
	include ENGINE_DIR . '/classes/mysql.php';
	include ENGINE_DIR . '/data/dbconfig.php';
	include ENGINE_DIR . '/modules/functions.php';
}

$goodQualityArray = ['WEB-DL' => 1, 'WEB.DL' => 2, 'WEB-DLRip' => 3, 'BDRip' => 4, 'BluRay' => 5, 'Blu-ray' => 6, 'BDRemux' => 7, 'Rip1080_HDR' => 8, 'HDTV' => 9, 'HDRip' => 10, 'HDTVRip' => 11];
$normalQualityArray = ['DVDScr' => 1, 'DVDRip' => 2, 'WEBRip' => 3, 'SATRip' => 4, 'DVB' => 5, 'DVD' => 6];
$badQualityArray = ['TC' => 1, 'TS' => 2, 'Cam' => 3, 'CAMRip' => 4];

$idKinopoisk = $dle_moonwalk_config['main']['id_kinopoisk'];
$idWorldArt = $dle_moonwalk_config['main']['id_worldart'];
if ($idKinopoisk || $idWorldArt) {
	$where = '';
	if ($dle_moonwalk_config['main']['disable_category']) {
		$where = "AND category NOT REGEXP '[[:<:]](" . implode('|', $dle_moonwalk_config['main']['disable_category']) . ")[[:>:]]'";
	}
	
	$xfieldsSeason = false;
	if ($dle_moonwalk_config['serial']['season_by_season']) {
		$xfieldsSeason = true;
	}
	
	dleMoonwalk::realize()->config($config, $dle_moonwalk_config, $dle_moonwalk_admin_lang);
	$dbNews = $db->query("SELECT id, xfields FROM " . PREFIX . "_post WHERE approve='1' AND date<'" . date ("Y-m-d H:i:s", time()) . "' {$where}");
	$i = 0;
	while ($row = $db->get_row($dbNews)) {
		$xfields = xfieldsdataload($row['xfields']);
		
		if (!$xfields[$idKinopoisk] && !$xfields[$idWorldArt]) {
			$i++;
			continue;
		}
		
		if ($xfields[$idKinopoisk]) {
			$fieldChoose = 'id_kinopoisk';
			$searchData = $xfields[$idKinopoisk];
		} else {
			$fieldChoose = 'id_worldart';
			$searchData = $xfields[$idWorldArt];
		}
		
		$json = dleMoonwalk::parseCron($fieldChoose, 'moonwalk', $searchData);
		$jsonArray = json_decode($json, true);
		
		if ($jsonArray['error']) {
			$i++;
			continue;
		}
		
		if ($jsonArray[0]['category'] == 'russian') {
			$category = 1;
		} elseif($jsonArray[0]['category'] == 'anime') {
			$category = 2;
		} else {
			$category = 0;
		}
		
		if ($jsonArray[0]['type'] == 'movie') {
			$getMovie = $db->super_query("SELECT id, voice, quality FROM " . PREFIX . "_dle_moonwalk WHERE newsId='{$row['id']}'");
			$stopQuality = false;
			$stopBadQuality = false;
			$updateTime = false;
			$qualitySiteUpdate = '';
			
			if (!$dle_moonwalk_config['movie']['quality_field'] || $dle_moonwalk_config['movie']['quality_field'] == '-') {
				$dle_moonwalk_config['movie']['quality_field'] = 'hide_quality_field';
			}
			
			foreach ($jsonArray as $key => $value) {
				if ($dle_moonwalk_config['main']['voice']) {
					if (in_array($value['translator_id'], $dle_moonwalk_config['main']['voice'])) {
						continue;
					}
				}
				
				if  ($category == 1 && !$value['translator']) {
					$value['translator'] = $dle_moonwalk_admin_lang[111];
				}
				
				$qualityMoonwalk = $value['camrip'] ? 'CAMRip' : 'HDRip';
				if ($value['source_type']) {
					$qualityMoonwalk = $value['source_type'];
				}
				
				if ($getMovie['quality'] && $getMovie['quality'] != $qualityMoonwalk && !isset($goodQualityArray[$getMovie['quality']])) {
					if (!isset($badQualityArray[$qualityMoonwalk])) {
						$qualitySiteUpdate = $qualityMoonwalk;
						$getMovie['voice'] = $value['translator'];
						$getMovie['translatorId'] = $value['translator_id'];
						$getMovie['video'] = $value['iframe_url'];
						$getMovie['updateMoonwalk'] = $value['added_at'];
						$updateTime = true;
					}
				} else if (!$getMovie['quality']) {
					if (isset($goodQualityArray[$qualityMoonwalk])) {
						$qualitySiteUpdate = $qualityMoonwalk;
						$getMovie['voice'] = $value['translator'];
						$getMovie['translatorId'] = $value['translator_id'];
						$getMovie['video'] = $value['iframe_url'];
						$getMovie['updateMoonwalk'] = $value['added_at'];
						$stopQuality = true;
					} elseif (isset($normalQualityArray[$qualityMoonwalk]) && !$stopQuality) {
						$qualitySiteUpdate = $qualityMoonwalk;
						$getMovie['voice'] = $value['translator'];
						$getMovie['translatorId'] = $value['translator_id'];
						$getMovie['video'] = $value['iframe_url'];
						$getMovie['updateMoonwalk'] = $value['added_at'];
						$stopBadQuality = true;
					} elseif(!$stopBadQuality && !$stopQuality) {
						$qualitySiteUpdate = $qualityMoonwalk;
						$getMovie['voice'] = $value['translator'];
						$getMovie['translatorId'] = $value['translator_id'];
						$getMovie['video'] = $value['iframe_url'];
						$getMovie['updateMoonwalk'] = $value['added_at'];
					}
				}
			}
			
			if ($dle_moonwalk_config['movie']['video_field']) {
				if ($dle_moonwalk_config['main']['ssl']) {
					$getMovie['video'] = preg_replace("#http(s)?://[^/]+/#iu", 'https://streamguard.cc/', $getMovie['video']);
				} elseif ($dle_moonwalk_config['main']['domain'] != '') {
					$getMovie['video'] = preg_replace("#http(s)?://[^/]+/#iu", 'http://' . $dle_moonwalk_config['main']['domain'] . '/', $getMovie['video']);
				}
				$xfields[$dle_moonwalk_config['movie']['video_field']] = $getMovie['video'];
			}
			
			if ($qualitySiteUpdate && $getMovie['quality'] != $qualitySiteUpdate) {
				if (!$xfields[$dle_moonwalk_config['movie']['quality_field']]) {
					$updateTime = true;
				}
				$xfields[$dle_moonwalk_config['movie']['quality_field']] = $qualitySiteUpdate;
			} else {
				continue;
			}
			
			$filecontents = [];
			foreach ($xfields as $xfielddataname => $xfielddatavalue) {
				if ($xfielddataname == '' || $xfielddatavalue == '') {
					continue;
				}
				
				$xfielddataname = str_replace('|', '&#124;', $xfielddataname);
				$xfielddataname = str_replace("\r\n", '__NEWL__', $xfielddataname);
				$xfielddatavalue = str_replace('|', '&#124;', $xfielddatavalue);
				$xfielddatavalue = str_replace("\r\n", '__NEWL__', $xfielddatavalue);
				$filecontents[] = $xfielddataname . '|' . $xfielddatavalue;
			}

			$filecontents = count($filecontents) ? $db->safesql(implode('||', $filecontents)) : '';

			$date = '';
			if ($updateTime) {
				$date = ", date='" . date('Y-m-d H:i:s', time()) . "'";
			}
			$db->query("UPDATE " . PREFIX . "_post SET xfields='{$filecontents}'{$date} WHERE id='{$row['id']}'");
			$updateDate = date('Y-m-d H:i:s', time());
			if ($getMovie['id'] && !$dle_moonwalk_config['block']['all_data']) {
				$db->query("UPDATE " . PREFIX . "_dle_moonwalk SET voice='{$getMovie['voice']}', translatorId='{$getMovie['translatorId']}', updateDate='{$updateDate}', updateMoonwalk='{$getMovie['updateMoonwalk']}', quality='{$qualitySiteUpdate}' WHERE id='{$getMovie['id']}'");
			} else {
				$db->query("INSERT INTO " . PREFIX . "_dle_moonwalk (newsId, voice, updateDate, updateMoonwalk, quality, category, typeVideo, translatorId) VALUES ('{$row['id']}', '{$getMovie['voice']}', '{$updateDate}', '{$getMovie['updateMoonwalk']}', '{$qualitySiteUpdate}', '{$category}', '0', '{$getMovie['translatorId']}')");
			}
		}
		else {
			$translatorSerial = [];
			$maxSeasonArray = [];
			$maxSeriaArray = [];
			
			$updateNews = false;
			if (!$dle_moonwalk_config['serial']['update_all_voice']) {
				$getMax = $db->super_query("SELECT MAX(season) as maxSeason, MAX(seria) as maxSeria FROM " . PREFIX . "_dle_moonwalk WHERE newsId='{$row['id']}'");
			}
			
			foreach ($jsonArray as $key => $value) {
				if ($dle_moonwalk_config['main']['voice']) {
					if (in_array($value['translator_id'], $dle_moonwalk_config['main']['voice'])) {
						continue;
					}
				}
				$updateDate = date('Y-m-d H:i:s', time());
				$voiceId = $db->safesql($value['translator_id']);
				$getData = $db->super_query("SELECT * FROM " . PREFIX . "_dle_moonwalk WHERE newsId='{$row['id']}' AND translatorId='{$voiceId}' ORDER BY id DESC LIMIT 1");
				
				if ($category == 1 && !$value['translator']) {
					$value['translator'] = $dle_moonwalk_admin_lang[111];
				} else {
					$value['translator'] = $db->safesql($value['translator']);
				}
				
				$qualityMoonwalk = $value['camrip'] ? 'CAMRip' : 'HDRip';
				if ($value['source_type']) {
					$qualityMoonwalk = $value['source_type'];
				}
				
				if ($xfieldsSeason) {
					$getData['season'] = $getData['season'] > 0 ? $getData['season'] : $xfields[$dle_moonwalk_config['serial']['season_number']];
					if (!$getData['season']) {
						continue;
					}
					
					$getSeason = array_filter($value['season_episodes_count'], function($item) use($getData) {
						return $item['season_number'] == $getData['season'];
					});
					
					$getSeason = array_values($getSeason);
					if ($getSeason[0]['season_number'] == $getData['season']) {
						if ($getSeason[0]['episodes_count'] > $getData['seria'] || !$getData['seria']) {
							if ($getData['newsId'] && !$dle_moonwalk_config['block']['all_data']) {
								$db->query("UPDATE " . PREFIX . "_dle_moonwalk SET seria='{$getSeason[0]['episodes_count']}', quality='{$qualityMoonwalk}', updateDate='{$updateDate}', updateMoonwalk='{$value['last_episode_time']}' WHERE translatorId='{$value['translator_id']}' AND newsId='{$row['id']}'");
								$updateNews = true;
							} else {
								$db->query("INSERT INTO " . PREFIX . "_dle_moonwalk (newsId, voice, season, seria, updateDate, updateMoonwalk, category, quality, typeVideo, translatorId) VALUES ('{$row['id']}', '{$value['translator']}', '{$getSeason[0]['season_number']}', '{$getSeason[0]['episodes_count']}', '{$updateDate}', '{$value['last_episode_time']}', '{$category}', '{$qualityMoonwalk}', '1', '{$value['translator_id']}');");
								if ($getSeason[0]['episodes_count'] == 1 || $dle_moonwalk_config['block']['all_data'] && ($dle_moonwalk_config['serial']['update_all_voice'] || ($getData['newsId'] && ($getSeason[0]['season_number'] > $getData['season'] || $getSeason[0]['season_number'] == $getData['season'] && $getSeason[0]['episodes_count'] > $getData['seria'])))) {
									$updateNews = true;
								}
							}
							$maxSeriaArray[$value['translator_id']] = $getSeason['episodes_count'];
							$translatorSerial[$value['translator_id']] = ['get' => $getData['newsId'], 'newsId' => $row['id'], 'quality' => $qualityMoonwalk, 'updateDate' => $updateDate, 'season' => $getSeason[0]['season_number'], 'seria' => $getSeason[0]['episodes_count'], 'translator' => $value['translator'], 'translatorId' => $value['translator_id']];
						}
					}
				} else {
					$getSeason = end($value['season_episodes_count']);
					
					if ($getSeason['season_number'] > $getData['season'] || $getSeason['season_number'] == $getData['season'] && $getSeason['episodes_count'] > $getData['seria'] || !$getData['season'] || !$getData['seria']) {
						$translatorSerial[$value['translator_id']] = ['get' => $getData['newsId'], 'newsId' => $row['id'], 'quality' => $qualityMoonwalk, 'updateDate' => $updateDate, 'season' => $getSeason['season_number'], 'seria' => $getSeason['episodes_count'], 'translator' => $value['translator'], 'translatorId' => $value['translator_id'], 'getSeason' => $getData['season'], 'getSeria' => $getData['seria'], 'updateMoonwalk' => $value['last_episode_time']];
						$maxSeriaArray[$getSeason['season_number']][$value['translator_id']] = $getSeason['episodes_count'];
					}
					$maxSeasonArray[] = $getSeason['season_number'];
				}
			}
			
			if (!$xfieldsSeason) {
				$getMaxSeason = max($maxSeasonArray);
				$translatorTempSerial = array_filter($translatorSerial, function($item) use($getMaxSeason) {
					return $item['season'] == $getMaxSeason;
				});
				$maxKey = array_search(max($maxSeriaArray[$getMaxSeason]), $maxSeriaArray[$getMaxSeason]);
				$dataForPost = $translatorTempSerial[$maxKey];
				
				foreach ($translatorTempSerial as $serialData) {
					if ($serialData['get'] && !$dle_moonwalk_config['block']['all_data']) {						
						$db->query("UPDATE " . PREFIX . "_dle_moonwalk SET season='{$serialData['season']}', seria='{$serialData['seria']}', quality='{$serialData['quality']}', updateDate='{$serialData['updateDate']}', updateMoonwalk='{$serialData['updateMoonwalk']}' WHERE translatorId='{$serialData['translatorId']}' AND newsId='{$row['id']}'");
						$updateNews = true;
					} else {
						$db->query("INSERT INTO " . PREFIX . "_dle_moonwalk (newsId, voice, season, seria, updateDate, category, quality, typeVideo, translatorId, updateMoonwalk) VALUES ('{$serialData['newsId']}', '{$serialData['translator']}', '{$serialData['season']}', '{$serialData['seria']}', '{$serialData['updateDate']}', '{$category}', '{$serialData['quality']}', '1', '{$serialData['translatorId']}', '{$serialData['updateMoonwalk']}');");
						if ($serialData['seria'] == 1 || $dle_moonwalk_config['block']['all_data'] && ($dle_moonwalk_config['serial']['update_all_voice'] || ($serialData['get'] && ($serialData['season'] > $serialData['getSeason'] || $serialData['season'] == $serialData['getSeason'] && $serialData['seria'] > $serialData['getSeria'])))) {
							$updateNews = true;
						}
					}
				}
			} else {
				$maxKey = array_search(max($maxSeriaArray), $maxSeriaArray);
				$dataForPost = $translatorSerial[$maxKey];
			}
			
			if (!$dle_moonwalk_config['serial']['update_all_voice'] && $updateNews) {
				$updateByOneVoice = array_filter($translatorTempSerial, function($item) use($getMax, $xfieldsSeason) {
					if ($xfieldsSeason) {
						return $item['seria'] > $getMax['maxSeria'];
					} else {
						return ($item['season'] > $getMax['maxSeason'] || $item['season'] == $getMax['maxSeason'] && $item['seria'] > $getMax['maxSeria']);
					}
				});
				
				if (!$updateByOneVoice) {
					$updateNews = false;
				}
			}
			
			if ($updateNews) {
				if ($dle_moonwalk_config['serial']['season_xfield']) {
					$xfields[$dle_moonwalk_config['serial']['season_xfield']] = dleMoonwalk::switchType($dataForPost['season'], $dle_moonwalk_config['serial']['season_format'], 'season');
				}
				
				if ($dle_moonwalk_config['serial']['seria_xfield']) {
					$xfields[$dle_moonwalk_config['serial']['seria_xfield']] = dleMoonwalk::switchType($dataForPost['seria'], $dle_moonwalk_config['serial']['seria_format'], 'seria');
				}
				
				$metaTitle = '';
				if ($dle_moonwalk_config['serial']['change_meta']) {
					$tagKeys = ['title_ru', 'title_en', 'translator', 'year'];
					$metaTitle = $dle_moonwalk_config['serial']['meta_title'];
					foreach ($tagKeys as $tagKey) {
						if (strpos($metaTitle, '{' . $tagKey . '}') !== false) {
							$metaTitle = str_replace('{' . $tagKey . '}', $jsonArray[0][$tagKey] ?: '', $metaTitle);
						}
						if ($tagKey == 'title_ru' || $tagKey == 'title_en') {
							$metaTitle = preg_replace('#\[tag-not-' . $tagKey . '\](.*?)\[\/tag-not-' . $tagKey . '\]#is', ($jsonArray[0][$tagKey] != '' ? '' : '$1'), $metaTitle);
						}
						if (strpos($metaTitle, '[tag-' . $tagKey . ']') !== false) {
							$metaTitle = preg_replace('#\[tag-' . $tagKey . '\](.*?)\[\/tag-' . $tagKey . '\]#is', ($jsonArray[0][$tagKey] != '' ? '$1' : ''), $metaTitle);
						}
					}
					
					if (strpos($metaTitle, '{season}') !== false) {
						$metaTitle = str_replace('{season}', $dataForPost['season'], $metaTitle);
						$metaTitle = preg_replace('#\[tag-season\](.*?)\[\/tag-season\]#is', '$1', $metaTitle);
					}
					
					if (strpos($metaTitle, '{seria}') !== false) {
						$metaTitle = str_replace('{seria}', $dataForPost['seria'], $metaTitle);
						$metaTitle = preg_replace('#\[tag-seria\](.*?)\[\/tag-seria\]#is', '$1', $metaTitle);
					}
					
					if (strpos($metaTitle, '{season-format-') !== false) {
						preg_match('#{season-format-([0-9]+)}#is', $metaTitle, $tagSeasonFormat);
						if ($tagSeasonFormat[1]) {
							$metaTitle = str_replace('{season-format-' . $tagSeasonFormat[1] . '}', dleMoonwalk::switchType($dataForPost['season'], $tagSeasonFormat[1], 'season'), $metaTitle);
							$metaTitle = preg_replace('#\[tag-season-format-' . $tagSeasonFormat[1] . '\](.*?)\[\/tag-season-format-' . $tagSeasonFormat[1] . '\]#is', '$1', $metaTitle);
						} else {
							$metaTitle = preg_replace('#\[tag-season-format-([0-9]+)\](.*?)\[\/tag-season-format-([0-9]+)\]#is', '', $metaTitle);
							$metaTitle = preg_replace('#{season-format-([0-9]+)}#is', '', $metaTitle);
						}
					}
					
					if (strpos($metaTitle, '{seria-format-') !== false) {
						preg_match('#{seria-format-([0-9]+)}#is', $metaTitle, $tagSeriaFormat);
						if ($tagSeriaFormat[1]) {
							$metaTitle = str_replace('{seria-format-' . $tagSeriaFormat[1] . '}', dleMoonwalk::switchType($dataForPost['seria'], $tagSeriaFormat[1], 'seria'), $metaTitle);
							$metaTitle = preg_replace('#\[tag-seria-format-' . $tagSeriaFormat[1] . '\](.*?)\[\/tag-seria-format-' . $tagSeriaFormat[1] . '\]#is', '$1', $metaTitle);
						} else {
							$metaTitle = preg_replace('#\[tag-seria-format-([0-9]+)\](.*?)\[\/tag-seria-format-([0-9]+)\]#is', '', $metaTitle);
							$metaTitle = preg_replace('#{seria-format-([0-9]+)}#is', '', $metaTitle);
						}
					}
					$metaTitle = ", metatitle='{$metaTitle}'";
				}
				$filecontents = [];
				foreach ($xfields as $xfielddataname => $xfielddatavalue) {
					if ($xfielddataname == '' || $xfielddatavalue == '') {
						continue;
					}
					
					$xfielddataname = str_replace('|', '&#124;', $xfielddataname);
					$xfielddataname = str_replace("\r\n", '__NEWL__', $xfielddataname);
					$xfielddatavalue = str_replace('|', '&#124;', $xfielddatavalue);
					$xfielddatavalue = str_replace("\r\n", '__NEWL__', $xfielddatavalue);
					$filecontents[] = $xfielddataname . '|' . $xfielddatavalue;
				}

				$filecontents = count($filecontents) ? $db->safesql(implode('||', $filecontents)) : '';				
				$date = ", date='" . date('Y-m-d H:i:s', time()) . "'";
				$db->query("UPDATE " . PREFIX . "_post SET xfields='{$filecontents}'{$metaTitle}{$date} WHERE id='{$row['id']}'");
			}
		}
		
		if ($i == 50) {
			ob_flush();
			flush();
			sleep(1);
			$i = 0;
		} else {
			$i++;
		}
	}
	
	if ($dle_moonwalk_config['block']['all_data']) {
		$db->query("DELETE FROM " . PREFIX . "_dle_moonwalk WHERE updateDate < DATE_SUB(CURRENT_DATE, INTERVAL {$dle_moonwalk_config['block']['block_date']} DAY)");
	}
	
	clear_cache(['news']);
}