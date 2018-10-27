<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev (https://lazydev.pro)
 * @version   1.1.1
 * @link      https://lazydev.pro
 */
 
include ENGINE_DIR . '/dle_moonwalk/config/dle_moonwalk.php';

$dateSort = 'updateDate';
if ($dle_moonwalk_config['block']['moonwalk_block_date']) {
	$dateSort = 'updateMoonwalk';
}
$sqlType = [];
if ($type) {
	$type = explode(',', $type);
	foreach ($type as $value) {
		if ($value == 'movie') {
			$sqlType[] = 0;
		} elseif ($value == 'serial') {
			$sqlType[] = 1;
		}
	}
}
$findType = '';
if ($sqlType) {
	$findType = 'AND d.typeVideo IN(' . implode(',', $sqlType) . ')';
}
$sqlCat = [];
if ($cat) {
	$cat = explode(',', $cat);
	foreach ($cat as $value) {
		if ($value == 'rus') {
			$sqlCat[] = 1;
		} elseif ($value == 'anime') {
			$sqlCat[] = 2;
		} elseif ($value == 'eng') {
			$sqlCat[] = 0;
		}
	}
}
$findCat = '';
if ($sqlCat) {
	$findCat = 'AND d.category IN(' . implode(',', $sqlType) . ')';
}
$cache = dle_cache('news_moonwalk_block', $config['skin'] . $findCat . $findType, false);
if ($cache) {
	echo $cache;
	return;
}

if ($dle_moonwalk_config['block']['one_voice']) {
	$db->query("SELECT d.voice, d.season, d.seria, d.updateDate, d.moonwalkCategory, d.updateMoonwalk, d.quality, d.typeVideo, d.translatorId, p.title, p.id, p.date, p.category, p.xfields, p.alt_name FROM ( SELECT IF(q.newsId = @p_news_id,0,1) AS is_max, q.id, @p_news_id := q.newsId AS newsId, q.voice, q.season, q.seria, q.updateDate, q.updateMoonwalk, q.category as moonwalkCategory, q.quality, q.typeVideo, q.translatorId FROM ( SELECT @p_news_id := NULL ) r CROSS JOIN " . PREFIX . "_dle_moonwalk q ORDER BY q.newsId DESC, q.season DESC, q.seria DESC, q.{$dateSort} DESC ) d LEFT JOIN " . PREFIX . "_post p ON(d.newsId=p.id) WHERE d.is_max AND d.{$dateSort} >= DATE_SUB(CURRENT_DATE, INTERVAL {$dle_moonwalk_config['block']['block_date']} DAY) AND p.approve=1 {$findCat} {$findType} ORDER BY d.{$dateSort} DESC LIMIT 0,{$dle_moonwalk_config['block']['block_news']}");
} else {
	$db->query("SELECT d.voice, d.season, d.seria, d.updateDate, d.updateMoonwalk, d.category as moonwalkCategory, d.quality, d.typeVideo, d.translatorId, p.title, p.id, p.date, p.category, p.xfields, p.alt_name FROM " . PREFIX . "_dle_moonwalk d LEFT JOIN " . PREFIX . "_post p ON(d.newsId=p.id) WHERE d.{$dateSort} >= DATE_SUB(CURRENT_DATE, INTERVAL {$dle_moonwalk_config['block']['block_date']} DAY) AND approve=1 {$findCat} {$findType} ORDER BY d.{$dateSort} DESC LIMIT 0,{$dle_moonwalk_config['block']['block_news']}");
}
$blockContent = [];
while ($row = $db->get_row()) {
	$date = date('Y-m-d', strtotime($row['updateDate']));
	$blockContent[$date][] = $row;
}

$tpl->load_template('dle_moonwalk/block.tpl');

$tplBlockContent = new dle_template();
$tplBlockContent->dir = TEMPLATE_DIR;

foreach ($blockContent as $updateDate => $news) {
	$date = strtotime($updateDate);
	if (date('Ymd', $date) == date('Ymd', $_TIME)) {
		$tpl->set('{date}', $lang['time_heute'] . langdate(', d F', $date));
	} elseif (date('Ymd', $date) == date('Ymd', ($_TIME - 86400))) {
		$tpl->set('{date}', $lang['time_gestern'] . langdate(', d F', $date));
	} else {
		$tpl->set('{date}', langdate('d F', $date));
	}
	
	$tplBlockContent->load_template('dle_moonwalk/block_content.tpl');
	
	$xfields = xfieldsload();
	foreach ($news as $data) {
		if ($data['typeVideo'] == 1) {
			$tplBlockContent->set_block("'\\[serial\\](.*?)\\[/serial\\]'si", '\\1');
			$tplBlockContent->set_block("'\\[movie\\](.*?)\\[/movie\\]'si", '');
			$tplBlockContent->set('{season}', $data['season']);
			$tplBlockContent->set('{seria}', $data['seria']);
		} else {
			$tplBlockContent->set_block("'\\[serial\\](.*?)\\[/serial\\]'si", '');
			$tplBlockContent->set_block("'\\[movie\\](.*?)\\[/movie\\]'si", '\\1');
			$tplBlockContent->set('{quality}', $data['quality']);
		}
		$tplBlockContent->set('{voice}', $data['voice']);
		
		$data['date'] = strtotime($data['date']);
		
		if ($config['allow_alt_url']) {
			if ($config['seo_type'] == 1 || $config['seo_type'] == 2) {
				if ($data['category'] && $config['seo_type'] == 2) {
					$data['category'] = intval($data['category']);
					$full_link = $config['http_home_url'] . get_url($data['category']) . '/' . $data['id'] . '-' . $data['alt_name'] . '.html';
				} else {
					$full_link = $config['http_home_url'] . $data['id'] . '-' . $data['alt_name'] . '.html';
				}
			} else {
				$full_link = $config['http_home_url'] . date('Y/m/d/', $data['date']) . $data['alt_name'] . '.html';
			}
		} else {
			$full_link = $config['http_home_url'] . 'index.php?newsid=' . $data['id'];
		}
		
		$tplBlockContent->set('{full-link}', $full_link);
		$tplBlockContent->set('{title}', stripslashes($data['title']));
		if (!$data['category']) {
			$my_cat = "---";
			$my_cat_link = "---";
		} else {
			$my_cat = [];
			$my_cat_link = [];
			$cat_list = explode(',', $data['category']);
			if (count($cat_list) == 1) {
				$my_cat[] = $cat_info[$cat_list[0]]['name'];
				$my_cat_link = get_categories($cat_list[0]);
			} else {
				foreach($cat_list as $element) {
					if ($element) {
						$my_cat[] = $cat_info[$element]['name'];
						if ($config['allow_alt_url']) {
							$my_cat_link[] = "<a href=\"" . $config['http_home_url'] . get_url($element) . "/\">{$cat_info[$element]['name']}</a>";
						} else {
							$my_cat_link[] = "<a href=\"$PHP_SELF?do=cat&category={$cat_info[$element]['alt_name']}\">{$cat_info[$element]['name']}</a>";
						}
					}
				}
				$my_cat_link = implode(', ', $my_cat_link);
			}
			$my_cat = implode(', ', $my_cat);
		}
		$tplBlockContent->set('{category-link}', $my_cat_link);
		$tplBlockContent->set('{category}', $my_cat);
		
		$data['date'] = strtotime($data['date']);
		if (date('Ymd', $data['date']) == date('Ymd', $_TIME)) {
			$tplBlockContent->set('{date}', $lang['time_heute'] . langdate(', d F', $data['date']));
		} elseif (date('Ymd', $data['date']) == date('Ymd', ($_TIME - 86400))) {
			$tplBlockContent->set('{date}', $lang['time_gestern'] . langdate(', d F', $data['date']));
		} else {
			$tplBlockContent->set('{date}', langdate('d F', $data['date']));
		}
		
		$news_date = $data['date'];
		$tplBlockContent->copy_template = preg_replace_callback("#\{date=(.+?)\}#i", "formdate", $tplBlockContent->copy_template);
		
		if (count($xfields)) {
			$xfieldsdata = xfieldsdataload($data['xfields']);
			foreach ($xfields as $value) {
				$preg_safe_name = preg_quote($value[0], "'");
				
				if ($value[20]) {
					$value[20] = explode(',', $value[20]);
					if ($value[20][0] && !in_array($member_id['user_group'], $value[20])) {
						$xfieldsdata[$value[0]] = '';
					}
				}

				if ($value[3] == 'yesorno') {
					if (intval($xfieldsdata[$value[0]])) {
						$xfgiven = true;
						$xfieldsdata[$value[0]] = $lang['xfield_xyes'];
					} else {
						$xfgiven = false;
						$xfieldsdata[$value[0]] = $lang['xfield_xno'];
					}
				} else {
					$xfgiven = true;
					if ($xfieldsdata[$value[0]] == '') {
						$xfgiven = false;
					}
				}
				
				if (!$xfgiven) {
					$tplBlockContent->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", '', $tplBlockContent->copy_template);
					$tplBlockContent->copy_template = str_ireplace("[xfnotgiven_{$value[0]}]", '', $tplBlockContent->copy_template);
					$tplBlockContent->copy_template = str_ireplace("[/xfnotgiven_{$value[0]}]", '', $tplBlockContent->copy_template);
				} else {
					$tplBlockContent->copy_template = preg_replace("'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", '', $tplBlockContent->copy_template);
					$tplBlockContent->copy_template = str_ireplace("[xfgiven_{$value[0]}]", '', $tplBlockContent->copy_template);
					$tplBlockContent->copy_template = str_ireplace("[/xfgiven_{$value[0]}]", '', $tplBlockContent->copy_template);
				}
				
				if (strpos($tplBlockContent->copy_template, "[ifxfvalue") !== false) {
					$tplBlockContent->copy_template = preg_replace_callback("#\\[ifxfvalue(.+?)\\](.+?)\\[/ifxfvalue\\]#is", "check_xfvalue", $tplBlockContent->copy_template);
				}
				
				$tplBlockContent->set("[xfvalue_{$value[0]}]", $xfieldsdata[$value[0]]);
			}
		}
		$tplBlockContent->compile('block_content');
	}
	$tpl->set('{block}', $tplBlockContent->result['block_content']);
	$tpl->compile('block_moonwalk');
	$tplBlockContent->global_clear();
}
create_cache('news_moonwalk_block', $tpl->result['block_moonwalk'], $config['skin'] . $findCat . $findType, false);
echo $tpl->result['block_moonwalk'];