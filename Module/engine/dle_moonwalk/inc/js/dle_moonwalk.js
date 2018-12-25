$(function() {
	$('body').on('click', '#DleMoonwalk-search-clear', function() {
		$('#DleMoonwalk-search-clear').css('display', 'none');
		$('#DleMoonwalk-search-results').hide();
		$('.DleMoonwalk-search-tbody').html('');
	});
});

function dleMoonwalkLoadPreviewPlayer() {
	var dleMoonwalk_preview_iframe = document.querySelector('#DleMoonwalk-preview');

	dleMoonwalk_preview_iframe.addEventListener('load', function() {
		if (!$('#DleMoonwalk-preview').attr('data-close')) {
			HideLoading();
			$('#previewPlayerModal').modal('show');
		}
	});
	
	$('.DleMoonwalk-search-preview').click(function() {
		$('#DleMoonwalk-preview').removeAttr('data-close');
		ShowLoading();
		dleMoonwalk_preview_iframe.setAttribute('src', $(this).data('url'));
	});
	
	$('#previewPlayerModal').on('hidden.bs.modal', function(e) {
		if (dleMoonwalk_preview_iframe.removeEventListener) {
			dleMoonwalk_preview_iframe.removeEventListener('load', function(){});
		} else if (dleMoonwalk_preview_iframe.detachEvent) {
			dleMoonwalk_preview_iframe.detachEvent('load', function(){});
		}

		$('#DleMoonwalk-preview').attr('data-close', true).attr('src', '');
	});
};

function dleMoonwalkSetData(hash) {
	$('.DleMoonwalk-set-data').click(function() {
		ShowLoading();
		
		var type = $(this).data('type');
		var token = $(this).data('token');
        var news_id = 0;
		if ($('[name=id]').length > 0) {
            news_id = $('[name=id]').val();
        }
		$.ajax({
			method: 'POST',
			url: '/engine/dle_moonwalk/inc/ajax/dle_moonwalk.php',
			data: { type: type, token: token, user_hash: hash, action: 'setData', news_id: news_id }
		}).done(function(msg) {
			HideLoading();
			try {
				msg = jQuery.parseJSON(msg);
				if (msg.icon == 'error') {
					Growl.error({
						title: msg.head,
						text: msg.text
					});
				} else {
					$.each(msg.api, function(key, value) {
						if (msg.api[key] !== undefined) {
							if (key == 'p.title' || key == 'p.short_story' || key == 'p.full_story') {
								var idKey = key.replace('p.', '');
								if (idKey == 'short_story' || idKey == 'full_story') {
									if (msg.config['editor'] == 1) {
										$('#' + idKey).froalaEditor('html.set', value);
									} else if (msg.config['editor'] == 0) {
										$('#' + idKey).val(value);
									} else if (msg.config['editor'] == 2) {
										tinymce.get(idKey).setContent(value);
									}
								} else {
									$('#' + idKey).val(value);
								}
							} else if (key == 'meta_title') {
								$('[name=meta_title]').val(value);
							} else {
								if ($('[name="xfield[' + key + ']"]')) {
									var xfield = $('[name="xfield[' + key + ']"]');
									var typeDom = xfield[0].tagName;
									if (typeDom == 'SELECT') {
										var xfValue = $('[name="xfield[' + key + ']"] option').filter(function() { return $(this).html() == value; }).val();
										$('[name="xfield[' + key + ']"] option[value=' + xfValue + ']').attr('selected', 'selected');
										xfield.selectpicker('refresh');
									} else if (xfield.data('rel') == 'links' && typeDom == 'INPUT') {
										xfield.tokenfield('setTokens', value);
									} else if (typeDom == 'TEXTAREA') {
										if (xfield.hasClass('wysiwygeditor')) {
											if (msg.config['editor'] == 1) {
												xfield.froalaEditor('html.set', value);
											} else if (msg.config['editor'] == 2) {
												tinymce.get(key).setContent(value);
											}
										} else {
											xfield.val(value);
										}
									} else if (typeDom == 'INPUT' && xfield.data('switchery')) {
										xfield.trigger('click');
									} else if (typeDom == 'INPUT' && xfield[0].type == 'text') {
										xfield.val(value);
									}
								}
							}
						}
					});
                    
                    if (msg.cat) {
                        $.each(msg.cat, function(key, cat) {
                            $('#category > option[value="'+cat+'"]').attr('selected', 'selected');
                        });
                    
                        $('#category').trigger('chosen:updated');
                    }
                    
                    if (msg.poster['field'] !== undefined) {
                        var returnbox = msg.poster['returnbox'];
                        var returnval = msg.poster['xfvalue'];

                        returnbox = returnbox.replace(/&lt;/g, "<");
                        returnbox = returnbox.replace(/&gt;/g, ">");
                        returnbox = returnbox.replace(/&amp;/g, "&");

                        $('#uploadedfile_' + msg.poster['field']).html( returnbox );
                        $('#xf_' + msg.poster['field']).val(returnval);

                        $('#xfupload_' + msg.poster['field'] + ' .qq-upload-button, #xfupload_' + msg.poster['field'] + ' .qq-upload-button input').attr('disabled', 'disabled');
                    }
				}
			} catch (e) {
				
			}
		});
	});
};

var miniLang = {
	title: 'Пустой заголовок новости',
	id_kinopoisk: 'Пустое поле ID Кинопоиск',
	id_worldart: 'Пустое поле ID World-Art',
	id_pornolab: 'Пустое поле ID Pornolab',
};

function dleMoonwalkAlert(message, title) {

	$("#dlepopup").remove();

	$("body").append("<div id='dlepopup' class='dle-alert' title='" + title + "' style='display:none'>"+ message +"</div>");

	$('#dlepopup').dialog({
		autoOpen: true,
		width: 550,
		minHeight: 160,
		resizable: false,
	});
};

function parseDleMoonwalk(hash) {
	$('#DleMoonwalk-search-clear').css('display', 'none');
	var fieldChoose = $('[name=optionChoose]').val();
	var searchData = '';
	if (fieldChoose == 'title') {
		searchData = $('#title').val().trim();
	} else if (configField[''+fieldChoose+'']) {
		searchData = $('#xf_' + configField[''+fieldChoose+'']).val().trim();
	}
	
	if (searchData == '') {
		Growl.error({
			title: 'Ошибка',
			text: miniLang[''+fieldChoose+'']
		});
		return;
	}
		
	ShowLoading();
	$.ajax({
		method: 'POST',
		url: '/engine/dle_moonwalk/inc/ajax/dle_moonwalk.php',
		data: { fieldChoose: fieldChoose, searchData: searchData, user_hash: hash, action: 'searchAdmin' }
	}).done(function(msg) {
		HideLoading();
		try {
			msg = jQuery.parseJSON(msg);
			if (msg.icon == 'error') {
				Growl.error({
					title: msg.head,
					text: msg.text
				});
			} else {
				if (msg.error != '') {
					$('#DleMoonwalk-search-notfound').html(msg.error).show();
					$('#DleMoonwalk-search-results').hide();
					$('.DleMoonwalk-search-tbody').html('');
				} else {
					$('#DleMoonwalk-search-notfound').html('').hide();
					$('#DleMoonwalk-search-results').show();
					$('#DleMoonwalk-search-clear').css('display', 'inline-block');
					$('.DleMoonwalk-search-tbody').html(msg.content);
					dleMoonwalkLoadPreviewPlayer();
					dleMoonwalkSetData(hash);
					$('[data-rel=alertAdv]').popover({container:'body'});
				}
			}
		} catch (e) {
			
		}
	});
};