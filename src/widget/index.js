;(function ($) {
  "use strict"

  var nocturneSwitcher = function ($scope, $) {
    let id = $scope.data("id")

    events()
    nocturne.reflectPreference()

    function events() {
      document.querySelectorAll(".js-dark-mode-trigger-" + id).forEach(trigger => {
        trigger.addEventListener("click", e => onClick(e))
      })

      // sync with system changes
      window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", ({ matches: isDark }) => {
        nocturne.theme.value = isDark ? "dark" : "light"
        nocturne.setPreference()
      })
    }

    function onClick(e) {
      e.preventDefault()
      nocturne.theme.value = nocturne.theme.value === "light" ? "dark" : "light"
      nocturne.setPreference()
    }
  }

  jQuery(window).on("elementor/frontend/init", () => {
    elementorFrontend.hooks.addAction("frontend/element_ready/nocturne-switcher.default", nocturneSwitcher)
  })
})(jQuery)
