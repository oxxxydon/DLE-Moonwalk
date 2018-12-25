function selectVoice() {
	if (this.classList.contains('activeVoiceName')) {
		return;
	}
	document.querySelectorAll('#voiceList > li.activeVoiceName')[0].classList.remove('activeVoiceName');
	this.classList.add('activeVoiceName');
	var iframePlayer = document.getElementById('MoonPlayer');
	iframePlayer.src = this.dataset.token;
}

var xhr; 
if (window.XMLHttpRequest) {
	xhr = new XMLHttpRequest();
} else {
	xhr = new ActiveXObject('Microsoft.XMLHTTP');
}

xhr.open('POST', '/engine/dle_moonwalk/ajax/dle_moonwalk.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');

var response;

xhr.onreadystatechange = function() {
	if (xhr.readyState == 4 && xhr.status == 200) {
		response = xhr.responseText;
		if (response) {
			var moonVideos = document.getElementById('moonVideos');
			moonVideos.innerHTML = response;
			var loadPlayerMoon = document.getElementById('loadPlayerMoon');
			loadPlayerMoon.style.display = 'none';
			document.querySelectorAll('#voiceList > li:first-child')[0].classList.add('activeVoiceName');
			var items = document.getElementsByClassName('voiceName');
			for (var i = 0; i < items.length; i++) {
				items[i].addEventListener('click', selectVoice);
			}
		}
	}
}

var newsId = document.getElementById('moonVideos').dataset.id;
if (newsId) {
	var moonVideos = document.getElementById('moonVideos');
	moonVideos.innerHTML = '<div id="nothingForU"><div id="playerFilmMoon"><div id="loadBlock"></div><div id="loadPlayerMoon"></div></div></div>';
	var loadPlayerMoon = document.getElementById('loadPlayerMoon');
    loadPlayerMoon.style.display = 'block';
	xhr.send('newsId=' + newsId);
}