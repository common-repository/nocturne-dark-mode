(function($) {
	"use strict";

	var $window = $(window);
	window.nocturne = {};
	nocturne.storageKey = "theme-preference"

	nocturne.getColorPreference = function() {
		if (document.documentElement.getAttribute("data-scheme") === "dark") {
			return "dark";
		}

		if (localStorage.getItem(nocturne.storageKey)) {
			return localStorage.getItem(nocturne.storageKey);
		} else {
			return window.matchMedia("(prefers-color-scheme: dark)").matches
				? "dark"
				: "light";
		}
	}

	nocturne.theme = {
		value: nocturne.getColorPreference(),
	}

	nocturne.reflectPreference = function() {
		document.firstElementChild.classList.remove("light", "dark");
		document.firstElementChild.classList.add(nocturne.theme.value);
		
		document.querySelectorAll(".nocturne-dark-mode-trigger").forEach((trigger) => {
			trigger?.setAttribute("aria-label", nocturne.theme.value);
		});
	}

	nocturne.setPreference = function() {
		localStorage.setItem(nocturne.storageKey, nocturne.theme.value);
		nocturne.reflectPreference();
	}

	// Init
	nocturneDarkMode()

	function nocturneDarkMode() {

		events()
		nocturne.reflectPreference()

		function events() {
			window.addEventListener("load", () => {
				nocturne.reflectPreference();
				// front-end
				document.querySelectorAll(".js-dark-mode-trigger").forEach((trigger) => {
					trigger.addEventListener("click", (e) => onClick(e));
				});
			})

			// sync with system changes
			window
				.matchMedia("(prefers-color-scheme: dark)")
				.addEventListener("change", ({ matches: isDark }) => {
					nocturne.theme.value = isDark ? "dark" : "light";
					nocturne.setPreference();
			});
		}		

		function onClick(e) {
			e.preventDefault();
			nocturne.theme.value = nocturne.theme.value === "light" ? "dark" : "light";
			nocturne.setPreference();
		}
	}

})(jQuery);