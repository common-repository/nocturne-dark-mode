;(function ($) {
  wp.customize("dark_mode_bg_color_setting", function (value) {
    value.bind(function (newval) {
      $(".dark body").css("background-color", newval)
    })
  })
  wp.customize("dark_mode_headings_color_setting", function (value) {
    value.bind(function (newval) {
      $(".dark h1,.dark h2,.dark h3,.dark h4,.dark h5,.dark h6").css("color", newval)
    })
  })
  wp.customize("dark_mode_text_color_setting", function (value) {
    value.bind(function (newval) {
      $(".dark body").css("color", newval)
    })
  })

  // Floating toggle
  wp.customize("dark_mode_floating_toggle_bg_color_setting", function (value) {
    value.bind(function (newval) {
      if (newval && newval.trim() !== "") {
        $(".nocturne-dark-mode-floating-trigger").css("backgroundColor", newval)
      } else {
        $(".nocturne-dark-mode-floating-trigger").css("backgroundColor", "#ffffff")
      }
    })
  })

  wp.customize("dark_mode_floating_toggle_border_color_setting", function (value) {
    value.bind(function (newval) {
      if (newval && newval.trim() !== "") {
        $(".nocturne-dark-mode-floating-trigger").css("borderColor", newval)
      } else {
        $(".nocturne-dark-mode-floating-trigger").css("borderColor", "#e7e8ec")
      }
    })
  })

  wp.customize("dark_mode_floating_toggle_icon_color_setting", function (value) {
    value.bind(function (newval) {
      if (newval && newval.trim() !== "") {
        $(".nocturne-dark-mode-floating-trigger .dark-mode-light").css("fill", newval)
      } else {
        $(".nocturne-dark-mode-floating-trigger .dark-mode-light").css("fill", "#131740")
      }
    })
  })

  wp.customize("dark_mode_floating_toggle_dark_mode_bg_color_setting", function (value) {
    value.bind(function (newval) {
      if (newval && newval.trim() !== "") {
        $(".dark .nocturne-dark-mode-floating-trigger").css("backgroundColor", newval)
      } else {
        $(".dark .nocturne-dark-mode-floating-trigger").css("backgroundColor", "#ffffff26")
      }
    })
  })

  wp.customize("dark_mode_floating_toggle_dark_mode_icon_color_setting", function (value) {
    value.bind(function (newval) {
      if (newval && newval.trim() !== "") {
        $(".nocturne-dark-mode-floating-trigger .dark-mode-dark").css("fill", newval)
      } else {
        $(".nocturne-dark-mode-floating-trigger .dark-mode-dark").css("fill", "#ffffff")
      }
    })
  })

  // Offset
  wp.customize("dark_mode_toggle_bottom_offset_setting", function (value) {
    value.bind(function (newval) {
      if (newval && newval.trim() !== "") {
        $(".nocturne-dark-mode-floating-trigger").css("bottom", newval + "px")
      } else {
        $(".nocturne-dark-mode-floating-trigger").css("bottom", "24px")
      }
    })
  })
  wp.customize("dark_mode_toggle_right_offset_setting", function (value) {
    value.bind(function (newval) {
      if (newval && newval.trim() !== "") {
        $(".nocturne-dark-mode-floating-trigger").css("right", newval + "px")
      } else {
        $(".nocturne-dark-mode-floating-trigger").css("right", "24px")
      }
    })
  })
})(jQuery)
