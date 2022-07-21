/*
Bedrock JavaScript files
Author: Tim Kaye
*/
document.addEventListener('DOMContentLoaded', function() {
	'use strict'; // satisfy code inspectors

	/* SHOW AND HIDE MENU AND TOGGLE BUTTONS ON MOBILE */
	if (window.matchMedia("screen and (max-width: 899px)").matches) {
		document.getElementById('menu-toggle').addEventListener('click', function() {
			this.setAttribute('hidden', 'hidden');
			document.getElementById('main-menu').style.display = 'block';
			document.getElementById('menu-toggle-close').removeAttribute('hidden');
			document.getElementById('menu-toggle-close').focus();
		}, false);
		document.getElementById('menu-toggle-close').addEventListener('click', function() {
			this.setAttribute('hidden', 'hidden');
			document.getElementById('main-menu').style.display = 'none';
			document.getElementById('menu-toggle').removeAttribute('hidden');
			document.getElementById('menu-toggle').focus();
		}, false);
	}

	/* SHOW AND HIDE SUB-MENUS ON GENERAL CLASSICPRESS MENU */
	const parents = document.getElementsByClassName('menu-item-has-children');
	for (let i = 0, n = parents.length; i < n; i++) {
		parents[i].addEventListener('mouseover', function() {
			parents[i].querySelector('.sub-menu').style.display = 'flex';
		}, false);
		parents[i].addEventListener('mouseout', function() {
			parents[i].querySelector('.sub-menu').style.display = 'none';
		}, false);
	}

	/* RELOAD ON RESIZE BEYOND MEDIA QUERY BREAKPOINT */
	var windoe = window;
	var windowWidth = window.innerWidth;

	window.addEventListener('resize', function() {
		if ((windowWidth >= 900 && window.innerWidth < 900) || (windowWidth < 900 && window.innerWidth >= 900)) {
			if (windoe.RT) {
				clearTimeout(windoe.RT);
			}
			windoe.RT = setTimeout(function() {
				this.location.reload(false); /* false to get page from cache */
			}, 100);
		}
	}, false);

});
