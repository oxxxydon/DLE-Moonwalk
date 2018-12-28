<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   2.0.0
 * @link      https://lazydev.pro/
 */
 
if (!defined('DATALIFEENGINE')) {
	die('Hacking attempt!');
}

define('FOLDER_PREFIX', date('Y-m') . '/');

class dleMoonwalk
{
	private static $instance = null;
	private static $dle_config = [], $moonwalk_config = [], $moonwalk_lang = [];
	private static $cache_type = '', $cache_id = '';
	private static $api = [
		'db' => 'http://dlemoon.online/api',
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
	
	public static function parseAdmin($fieldChoose, $searchData)
	{
		self::$cache_type = self::$api['db'];
		self::$cache_id = urlencode($searchData);
		$j = self::getCache();
		if ($j == false) {
			$url = self::$api['db'] . '/videos.json?api_token=' . self::$moonwalk_config['main']['api_token'] . '&' .self::$api['field'][$fieldChoose] . '=' . urlencode($searchData);
			self::$json = self::curl($url);
		} else {
			self::$json = $j;
		}
		
		return self::$instance;
	}
	
	public static function parseCron($fieldChoose, $searchData)
	{
		$url = self::$api['db'] . '/videos.json?api_token=' . self::$moonwalk_config['main']['api_token'] . '&' .self::$api['field'][$fieldChoose] . '=' . urlencode($searchData);
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
				if ($voice == 'n/a' && $array['category'] == 'russian') {
					$voice = self::$moonwalk_lang[111];
				}
				$typeInt = $array['type'] == 'movie' ? 0 : 1;
				
				$temp = $template;
				if ($array['category'] == 'russian') {
					$temp = str_replace('{category}', self::$moonwalk_lang[125], $temp);
				} elseif($array['category'] == 'anime') {
					$temp = str_replace('{category}', self::$moonwalk_lang[127], $temp);
				} else {
					$temp = str_replace('{category}', self::$moonwalk_lang[126], $temp);
				}
				$temp = str_replace('{year}', $array['year'], $temp);
				$temp = str_replace('{type}', $type, $temp);
				$temp = str_replace('{title}', $title, $temp);
				$temp = preg_replace("#\[adv\](.*?)\[/adv\]#is", $adv, $temp);

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
                
                if (self::$moonwalk_config['main']['ssl']) {
                    $array['iframe_url'] = preg_replace("#http(s)?://[^/]+/#iu", 'https://streamguard.cc/', $array['iframe_url']);
                } elseif (self::$moonwalk_config['main']['domain'] != '') {
                    $array['iframe_url'] = preg_replace("#http(s)?://[^/]+/#iu", 'http://' . self::$moonwalk_config['main']['domain'] . '/', $array['iframe_url']);
                }
                
				$temp = str_replace('{quality}', $quality, $temp);
				$temp = str_replace('{voice}', trim($voice), $temp);
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

	public static function setData($type, $token, $member_id, $user_group, $db, $news_id)
	{
		$url = self::$api['db'] . '/' . $type . '.json?api_token=' . self::$moonwalk_config['main']['api_token'] . '&token=' . urlencode($token);
		$json = self::curl($url);
		$json = json_decode($json, true);
		if ($json['kinopoisk_id']) {
            $keyVoice = 'kinopoisk_id=' . urlencode($json['kinopoisk_id']);
        } elseif ($json['world_art_id']) {
            $keyVoice = 'world_art_id=' . urlencode($json['world_art_id']);
        }
        
        $url = self::$api['db'] . '/videos.json?api_token=' . self::$moonwalk_config['main']['api_token'] . '&' . $keyVoice;
		$allVoice = self::curl($url);
        $allVoice = json_decode($allVoice, true);
        $allVoiceArray = [];
        foreach ($allVoice as $keyVoice => $dataVoice) {
            $dataVoice['translator'] = $dataVoice['translator'] ? $dataVoice['translator'] : 'n/a';
            if ($dataVoice['translator'] == 'n/a' && $dataVoice['category'] == 'russian') {
                $dataVoice['translator'] = self::$moonwalk_lang[111];
            }
            $allVoiceArray[] = trim($dataVoice['translator']);
        }
        
        unset($allVoice);
        $allVoiceArray = implode(', ', $allVoiceArray);
        $json['translator-all'] = $allVoiceArray;
        
        $getPoster = [];
        if (self::$moonwalk_config['poster']['upload'] == 1 && $json['material_data']['poster']) {
            $getPoster = self::uploadPoster($json['material_data']['poster'], $member_id, $user_group, $db, $news_id);
        }
        
		$dataArray = [];
		$fieldData = self::$moonwalk_config['data'];
		
		if (self::$moonwalk_config[$type]['change_meta'] == 1 && self::$moonwalk_config[$type]['meta_title'] != '') {
			$fieldData['meta_title'] = self::$moonwalk_config[$type]['meta_title'];
		}
		
		$keys = ['title_ru', 'title_en', 'translator', 'translator-all', 'year', 'description', 'countries', 'actors', 'genres', 'directors', 'age', 'kinopoisk_rating', 'kinopoisk_votes', 'imdb_rating', 'imdb_votes', 'iframe_url', 'trailer_iframe_url', 'kinopoisk_id', 'world_art_id', 'duration', 'quality', 'block'];
		
		if (isset($json['duration']['human'])) {
			$json['duration'] = $json['duration']['human'];
		}

		$json['quality'] = $json['camrip'] ? 'CAMRip' : 'HDRip';
		if ($json['source_type']) {
			$json['quality'] = $json['source_type'];
		}
		
		if ($type == 'serial') {
			$season = end($json['season_episodes_count']);
			$seria = $season['episodes_count'];
			$season = $season['season_number'];
		}

        $data_xf = xfieldsload();
        $deleteField = false;
        $arrayPoster = [];
		foreach ($fieldData as $field => $data) {
			$tempData = $data;
			foreach ($keys as $key) {
				if ($key == 'block') {
					if ((bool)$json['block']['block_ru']) {
						$tempData = preg_replace('#\[tag-block-ru\](.*?)\[\/tag-block-ru\]#is', '$1', $tempData);
						$tempData = preg_replace('#\[tag-not-block-ru\](.*?)\[\/tag-not-block-ru\]#is', '', $tempData);
					} else {
						$tempData = preg_replace('#\[tag-block-ru\](.*?)\[\/tag-block-ru\]#is', '', $tempData);
						$tempData = preg_replace('#\[tag-not-block-ru\](.*?)\[\/tag-not-block-ru\]#is', '$1', $tempData);
					}
					
					if ((bool)$json['block']['block_ua']) {
						$tempData = preg_replace('#\[tag-block-ua\](.*?)\[\/tag-block-ua\]#is', '$1', $tempData);
						$tempData = preg_replace('#\[tag-not-block-ua\](.*?)\[\/tag-not-block-ua\]#is', '', $tempData);
					} else {
						$tempData = preg_replace('#\[tag-block-ua\](.*?)\[\/tag-block-ua\]#is', '', $tempData);
						$tempData = preg_replace('#\[tag-not-block-ua\](.*?)\[\/tag-not-block-ua\]#is', '$1', $tempData);
					}
				} elseif ($key == 'iframe_url') {
					$tempData = preg_replace('#\[tag-video\](.*?)\[\/tag-video\]#is', ($json[$key] != '' ? '$1' : ''), $tempData);
					if (self::$moonwalk_config['main']['ssl']) {
						$json[$key] = preg_replace("#http(s)?://[^/]+/#iu", 'https://streamguard.cc/', $json[$key]);
					} elseif (self::$moonwalk_config['main']['domain'] != '') {
						$json[$key] = preg_replace("#http(s)?://[^/]+/#iu", 'http://' . self::$moonwalk_config['main']['domain'] . '/', $json[$key]);
					}
					
					if (self::$moonwalk_config['main']['geo_block'] == 1) {
						$videoParam = [];
						if ((bool)$json['block']['block_ru']) {
							$videoParam[] = 'block_ru=1';
						}
						if ((bool)$json['block']['block_ua']) {
							$videoParam[] = 'block_ua=1';
						}
						if ($videoParam) {
							$videoParam = implode('&', $videoParam);
							$json[$key] .= '?' . $videoParam;
						}
					}
					
					$tempData = str_replace('{video}', $json[$key], $tempData);
				} elseif($key == 'trailer_iframe_url') {
					$tempData = preg_replace('#\[tag-trailer\](.*?)\[\/tag-trailer\]#is', ($json[$key] != '' ? '$1' : ''), $tempData);
					$tempData = str_replace('{trailer}', $json[$key], $tempData);
				} else {
					if ($key == 'title_ru' || $key == 'title_en') {
						$tempData = preg_replace('#\[tag-not-' . $key . '\](.*?)\[\/tag-not-' . $key . '\]#is', ($json[$key] != '' ? '' : '$1'), $tempData);
					}
					
					if ($json[$key]) {
						$json[$key] = trim(stripslashes($json[$key]));
					} elseif ($json['material_data'][$key] && is_array($json['material_data'][$key])) {
						$json[$key] = stripslashes(implode(', ', $json['material_data'][$key]));
					} elseif ($json['material_data'][$key]) {
                        $json[$key] = stripslashes($json['material_data'][$key]);
					}
					
					$tempData = preg_replace('#\[tag-' . $key . '\](.*?)\[\/tag-' . $key . '\]#is', ($json[$key] != '' ? '$1' : ''), $tempData);
					$tempData = str_replace('{' . $key . '}', stripslashes($json[$key]), $tempData);
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
			
            if ($getPoster && strpos($tempData, '{poster}') !== false) {
                $this_xf = [];
                if ($field != 'p.short_story' && $field != 'p.full_story') {
                    $this_xf = array_filter($data_xf, function($item) use($field) {
                        return $item[0] == $field;
                    });
                    reset($this_xf);
                }
                $tempData = preg_replace('#\[tag-poster\](.*?)\[\/tag-poster\]#is', '\\1', $tempData);
                if ($field == 'p.short_story' || $field == 'p.full_story' || $this_xf[0][3] != 'image') {
                    if (self::$dle_config['allow_admin_wysiwyg'] == 1) {
                        $getPoster['thumb'] = $getPoster['thumb'] ?: $getPoster['original'];
                        $poster = "<a class=\"highslide\" href=\"{$getPoster['original']}\" target=\"_blank\"><img src=\"{$getPoster['thumb']}\" alt=\"\" class=\"fr-dib fr-draggable\"></a>";
                    } elseif (self::$dle_config['allow_admin_wysiwyg'] == 2) {
                        $getPoster['thumb'] = $getPoster['thumb'] ?: $getPoster['original'];
                        $poster = "<a href=\"{$getPoster['original']}\" class=\"highslide\" target=\"_blank\" rel=\"noopener\" data-mce-href=\"{$getPoster['original']}\"><img src=\"{$getPoster['thumb']}\" alt=\"\" data-mce-src=\"{$getPoster['thumb']}\"></a>";
                    } else {
                        $poster = $getPoster['thumb'] != '' ? '[thumb]' . $getPoster['original'] . '[/thumb]' : $getPoster['original'];
                    }
                    $tempData = str_replace('{poster}', $poster, $tempData);
                } elseif ($this_xf[0][3] == 'image') {
                    $deleteField = $field;
                    $xf_id = md5($getPoster['folder']);
                    $fileName = explode('/', $getPoster['folder']);
                    $fileName = end($fileName);
                    $getPoster['thumb'] = $getPoster['thumb'] ?: $getPoster['original'];
					$return_box = "<div id=\"xf_{$xf_id}\" class=\"uploadedfile\" data-id=\"{$getPoster['folder']}\" data-alt=\"\"><div class=\"info\">{$fileName}</div><div class=\"uploadimage\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $getPoster['thumb'] . "\" /></div><div class=\"info\"><a href=\"#\" onclick=\"xfaddalt('".$xf_id."', '".$field."');return false;\">Описание</a><br><a href=\"#\" onclick=\"xfimagedelete('".$field."','".$getPoster['folder']."');return false;\">Удалить</a></div></div>";

                    $arrayPoster = ['returnbox' => $return_box, 'xfvalue' => $getPoster['folder'], 'field' => $field];
                }
            }
            
            if (strpos($tempData, '{kinopoisk_rating-') !== false && $json['kinopoisk_rating'] > 0) {
                preg_match("#\{kinopoisk_rating-(\d+)\}#is", $tempData, $matches);
                if ($matches[1]) {
                    $tempData = str_replace('{kinopoisk_rating-' . $matches[1] . '}', number_format((float)$json['kinopoisk_rating'], intval($matches[1]), '.', ''), $tempData);
                }
            }
            
            if (strpos($tempData, '{imdb_rating-') !== false && $json['imdb_rating'] > 0) {
                preg_match("#\{imdb_rating-(\d+)\}#is", $tempData, $matches);
                if ($matches[1]) {
                    $tempData = str_replace('{imdb_rating-' . $matches[1] . '}', number_format((float)$json['imdb_rating'], intval($matches[1]), '.', ''), $tempData);
                }
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
		if ($deleteField !== false) {
            unset($fieldData[$deleteField]);
        }
        
        $catArray = [];
        if ($json['material_data']['genres']) {
            
            foreach ($json['material_data']['genres'] as $ganre) {
                $ganreTranslit = totranslit($ganre);
                if (self::$moonwalk_config['category'][$ganreTranslit] > 0) {
                    $catArray[] = self::$moonwalk_config['category'][$ganreTranslit];
                }
            }
        }
        
		$dataJson = ['api' => $fieldData, 'config' => ['editor' => self::$dle_config['allow_admin_wysiwyg']], 'poster' => $arrayPoster, 'cat' => $catArray];
		return json_encode($dataJson);
	}
	
    public static function uploadPoster($img, $member_id, $user_group, $db, $news_id)
    {
        $type = explode('.', $img);
        $type = end($type);
		if (in_array($type, ['png', 'jpg', 'jpeg'])) {
			if (!is_dir(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX)) {
				@mkdir(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX, 0777);
				@chmod(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX, 0777);
				@mkdir(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . 'thumbs', 0777);
				@chmod(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . 'thumbs', 0777);
			}
			if (stripos($img, '.php') !== false) {
				return false;
			}
			if (stripos($img, '.phtm') !== false) {
				return false;
			}
            
            $filename = explode('/', $img);
            $filename = end($filename);
            if ($filename && is_dir(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX)) {
                $file_prefix = time() + rand(1, 100);
                $file_prefix .= '_';
                $filename = $file_prefix . $filename;
                if (copy($img, ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . $filename)) {
                    $fileFolder = FOLDER_PREFIX . $filename;
                    @chmod(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . $filename, 0666);
                    $added_time = time();
                    $sizeInfo = getimagesize(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . $filename);
                    if (!in_array($sizeInfo[2], [2, 3])) {
                        unlink(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . $filename);
                        return false;
                    }
                    
                    $thumb = new thumbnail(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . $filename);
                    $member_id['name'] = $db->safesql($member_id['name']);
                    $row = $db->super_query("SELECT id, images FROM " . PREFIX . "_images WHERE news_id='{$news_id}' AND author='{$member_id['name']}'");
                    if (!$row['id']) {
                        $inserts = FOLDER_PREFIX . $filename;
                        $db->query("INSERT INTO " . PREFIX . "_images (images, author, news_id, date) values ('{$inserts}', '{$member_id['name']}', '{$news_id}', '{$added_time}')");
                    } else {
                        $error_image = false;
                        if ($row['images']) {
                            $listimages = explode('|||', $row['images']);
                            foreach ($listimages as $dataimages) {
                                if ($dataimages == FOLDER_PREFIX . $filename) {
                                    $error_image = true;
                                    break;
                                }
                            }
                        }
                        
                        if (!$error_image) {
                            if (!$listimages) {
                                $listimages = [];
                            }
                            $listimages[] = FOLDER_PREFIX . $filename;
                            $row['images'] = implode('|||', $listimages);
                            
                            if (dle_strlen($row['images'], self::$dle_config['charset']) < 65000) {
                                $db->query("UPDATE " . PREFIX . "_images SET images='{$row['images']}' WHERE news_id='{$news_id}' AND author='{$member_id['name']}'");
                            }
                        }
                    }
                    
                    $thumb_bool = false;
                    $sizeTumb = self::$moonwalk_config['poster']['size_tumb'];
                    $typeSizeTumb = self::$moonwalk_config['poster']['type_size_tumb'];
                    $quality = self::$moonwalk_config['poster']['quality'];
                    $sizeImage = self::$moonwalk_config['poster']['size_poster'];
                    
                    if (intval($sizeTumb) > 0) {
                        $tumbSize = explode('x', $sizeTumb);
                        if (count($tumbSize) == 2 && intval($tumbSize[0]) > 0 && intval($tumbSize[1]) > 0) {
                            $tumbSize = intval($tumbSize[0]) . 'x' . intval($tumbSize[1]);
                        } elseif (intval($tumbSize[0]) > 0) {
                            $tumbSize = intval($tumbSize[0]);
                        }
                        
                        if (!is_array($tumbSize)) {
                            if ($thumb->size_auto($tumbSize, $typeSizeTumb)) {
                                $thumb->jpeg_quality($quality);
                                $thumb->save(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . 'thumbs/' . $filename);
                                @chmod(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . 'thumbs/' . $filename, 0666);
                                $thumb_bool = true;
                            }
                        }
                    }
                    
                    if (intval($sizeImage) > 0) {
                        $sizeImage = explode('x', $sizeImage);
                        if (count($sizeImage) == 2 && intval($sizeImage[0]) > 0 && intval($sizeImage[1]) > 0) {
                            $sizeImage = intval($sizeImage[0]) . 'x' . intval($sizeImage[1]);
                        } elseif (intval($sizeImage[0]) > 0) {
                            $sizeImage = intval($sizeImage[0]);
                        }
                        
                        if (!is_array($sizeImage)) {
                            $thumb = new thumbnail(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . $filename);
                            $thumb->jpeg_quality($quality);
                            $re_save = false;
                            
                            if ($thumb->size_auto($sizeImage, 0)) {
                                $re_save = true;
                            }
                        
                            if ($re_save) {
                                $thumb->save(ROOT_DIR . '/uploads/posts/' . FOLDER_PREFIX . $filename);
                            }
                        }
                    }
                    
                    $m = ['original' => self::$dle_config['http_home_url'] . 'uploads/posts/' . FOLDER_PREFIX . $filename, 'folder' => $fileFolder];
                    if ($thumb_bool) {
                        $m['thumb'] = self::$dle_config['http_home_url'] . 'uploads/posts/' . FOLDER_PREFIX . 'thumbs/' . $filename;
                    }
                    
                    return $m;
                }
            }
        }
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
    
    public static function setCache($data, $cache_key = false)
	{
        if ($cache_key === false) {
            $cache_key = self::$cache_id = md5(self::$cache_type . '_' . self::$cache_id);
        }
        
        file_put_contents(ENGINE_DIR . '/dle_moonwalk/cache/admin/' . $cache_key . '.tmp', $data, LOCK_EX);
        @chmod(ENGINE_DIR . '/dle_moonwalk/cache/admin/' . $cache_key . '.tmp', 0666);
	}
	
	public static function getCache($cache_key = false)
	{
        if ($cache_key === false) {
            $cache_key = self::$cache_id = md5(self::$cache_type . '_' . self::$cache_id);
        }

		$file = ENGINE_DIR . '/dle_moonwalk/cache/admin/' . $cache_key . '.tmp';
		$buffer = false;
		if (file_exists($file)) {
			$file_date = filemtime($file);
			$file_date = time() - $file_date;

			if ($file_date > (180 * 60)) {
				$buffer = false;
				@unlink($file);
			} else {
                $buffer = file_get_contents($file);
            }
		}
		return $buffer;
	}
    
    public static function getVideoCache($idFilm, $time)
	{
		$file = ENGINE_DIR . '/dle_moonwalk/cache/video/' . $idFilm . '.tmp';
		$buffer = false;
		if (file_exists($file)) {
			$file_date = filemtime($file);
			$file_date = time() - $file_date;

			if ($file_date > ((1440 * $time) * 60)) {
				$buffer = false;
				@unlink($file);
			} else {
                $buffer = file_get_contents($file);
            }
		}
		return $buffer;
	}
    
    public static function setVideoCache($idFilm, $data)
	{
        file_put_contents(ENGINE_DIR . '/dle_moonwalk/cache/video/' . $idFilm . '.tmp', $data, LOCK_EX);
        @chmod(ENGINE_DIR . '/dle_moonwalk/cache/video/' . $idFilm . '.tmp', 0666);
	}
    
    public static function isSSL()
    {
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
            || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
            || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
            || (isset($_SERVER['CF_VISITOR']) && $_SERVER['CF_VISITOR'] == '{"scheme":"https"}')
            || (isset($_SERVER['HTTP_CF_VISITOR']) && $_SERVER['HTTP_CF_VISITOR'] == '{"scheme":"https"}')
        ) {
            return true;
        } else {
            return false;
        }
    }
}
