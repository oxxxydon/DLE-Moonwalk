<?php
/**
 * DLE Moonwalk
 *
 * @copyright 2018 LazyDev
 * @version   2.0.0
 * @link      https://lazydev.pro/
 */
 
if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

function showLazyRow($title = '', $description = '', $field = '')
{
echo <<<HTML
<tr>
	<td class="col-xs-6 col-sm-6 col-md-7">
		<h6 class="media-heading text-semibold">{$title}</h6>
		<span class="text-muted text-size-small hidden-xs">{$description}</span>
	</td>
    <td class="col-xs-6 col-sm-6 col-md-5">{$field}</td>
</tr>
HTML;
}

function showLazyInput($data)
{
	$input_elemet = $data[3] ? " placeholder=\"{$data[3]}\"" : '';
	$input_elemet .= $data[4] ? ' disabled' : '';
	if ($data[1] == 'range') {
		$class = ' custom-range';
		$input_elemet .= $data[5] ? " step=\"{$data[5]}\"" : '';
		$input_elemet .= $data[6] ? " min=\"{$data[6]}\"" : '';
		$input_elemet .= $data[7] ? " max=\"{$data[7]}\"" : '';
	} elseif ($data[1] == 'number') {
		$class = ' w-9';
		$input_elemet .= $data[5] ? " min=\"{$data[5]}\"" : '';
		$input_elemet .= $data[6] ? " max=\"{$data[6]}\"" : '';
	}
return <<<HTML
	<input type="{$data[1]}" autocomplete="off" style="float: right;" value="{$data[2]}" class="form-control{$class}" name="{$data[0]}"{$input_elemet}>
HTML;
}

function makeLazyDropDown($options, $name, $selected)
{
	$output = "<select class=\"uniform\" name=\"$name\">\r\n";
	foreach ($options as $value => $description) {
		$output .= "<option value=\"$value\"";
		if ($selected == $value) {
			$output .= " selected ";
		}
		$output .= ">$description</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function makeLazyCheckBox($name, $selected, $disabled = false)
{
	$disabled = $disabled ? 'disabled' : '';
	$selected = $selected ? 'checked' : '';
	return "<label class=\"checkbox\"><input type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected} {$disabled}><span></span></label>";
}

function showLazySelect($name, $select, $placeholder = '', $multiple = true)
{
    $multiple = $multiple === true ? 'multiple' : '';
	return "<select name=\"{$name}\" class=\"form-control custom-select\" data-placeholder=\"{$placeholder}\" {$multiple}>" . $select . "</select>";
}

function lazySelect($data)
{
	foreach ($data[1] as $key => $val) {
		if ($data[2]) {
			$output .= "<option value=\"{$key}\"";
		} else {
			$output .= "<option value=\"{$val}\"";
		}
		if (is_array($data[3])) {
			foreach ($data[3] as $element) {
				if ($data[2] && $element == $key) {
					$output .= " selected ";
				} elseif (!$data[2] && $element == $val) {
					$output .= " selected ";
				}
			}
		} elseif ($data[2] && $data[3] == $key) {
			$output .= " selected ";
		} elseif (!$data[2] && $data[3] == $val) {
			$output .= " selected ";
		}
		$output .= ">{$val}</option>\n";
	}
	$input_elemet = $data[5] ? ' disabled' : '';
	$input_elemet .= $data[4] ? ' multiple' : '';
	$input_elemet .= $data[6] ? " data-placeholder=\"{$data[6]}\"" : '';
return <<<HTML
<select name="{$data[0]}" class="form-control custom-select" {$input_elemet}>
	{$output}
</select>
HTML;
}

function lazySelectDisable($data, $disable)
{
	$i = 0;
	foreach ($data[1] as $key => $val) {
		if ($data[2]) {
			$output .= "<option value=\"{$key}\"";
		} else {
			$output .= "<option value=\"{$val}\"";
		}
		
		if (is_array($data[3])) {
			foreach ($data[3] as $element) {
				if ($data[2] && $element == $key) {
					$output .= ' selected';
				} elseif (!$data[2] && $element == $val) {
					$output .= ' selected';
				}
			}
		} elseif ($data[2] && $data[3] == $key) {
			$output .= ' selected';
		} elseif (!$data[2] && $data[3] == $val) {
			$output .= ' selected';
		}
		if (!$disable[$i]) {
			$output .= ' disabled';
		}
		$output .= ">{$val}</option>\n";
		$i++;
	}
	$input_elemet = $data[5] ? ' disabled' : '';
	$input_elemet .= $data[4] ? ' multiple' : '';
	$input_elemet .= $data[6] ? " data-placeholder=\"{$data[6]}\"" : '';
return <<<HTML
<select name="{$data[0]}" class="form-control custom-select" {$input_elemet}>
	{$output}
</select>
HTML;
}

function showTrInline($name, $description, $type, $data)
{
echo <<<HTML
<tr>
	<td>
		<label style="float:left;" class="form-label">{$name}</label>
HTML;
	switch ($type) {
		case 'input':
			echo showLazyInput($data);
		break;
		case 'textarea':
			echo textareaLazyForm($data);
		break;
		default:
			echo $data;
		break;
	}
echo <<<HTML
</tr>
HTML;
}
	
function textareaLazyForm($data)
{
	$input_elemet = $data[2] ? " placeholder=\"{$data[2]}\"" : '';
	$input_elemet .= $data[3] ? ' disabled' : '';
return <<<HTML
	<textarea style="min-height:150px;max-height:150px;min-width:333px;max-width:100%;border: 1px solid #ddd;padding: 5px;" autocomplete="off" class="form-control" name="{$data[0]}"{$input_elemet}>{$data[1]}</textarea>
HTML;
}
