document.addEventListener('DOMContentLoaded', function() {
	'use strict';
	
	const infoButtons = document.getElementsByClassName('info-button');
	for (let i = 0, n = infoButtons.length; i < n; i++) {
		infoButtons[i].addEventListener('click', function() {
			let info = document.getElementById(infoButtons[i].dataset.info);
			if (info.hasAttribute('hidden')) {
				info.removeAttribute('hidden');
			} else {
				info.setAttribute('hidden', 'hidden');
			}
		});
	}
});
