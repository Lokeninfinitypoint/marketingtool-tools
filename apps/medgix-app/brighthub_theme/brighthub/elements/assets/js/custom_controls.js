(function ($) {
  "use strict";

  // Prevent multiple initialization
  var brighthubCustomControlsInitialized = false;
  var brighthubHooksRegistered = false;
  var brighthubResizeTimeout = null;

  /**
   * =========================
   * MODULE 2: Section Render
   * =========================
   */
  function brighthub_section_start_render() {
    // Prevent multiple hook registrations
    if (brighthubHooksRegistered) return;

    var _elementor = typeof elementor !== "undefined" ? elementor : elementorFrontend;
    if (!_elementor || !_elementor.hooks) return;

    function hex2rgba(color, opacity) {
      if (!color) return "rgb(0,0,0)";
      if (color[0] === "#") {
        color = color.substring(1);
      }
      var hex;
      if (color.length === 6) {
        hex = [color.substring(0, 2), color.substring(2, 4), color.substring(4, 6)];
      } else if (color.length === 3) {
        hex = [color[0] + color[0], color[1] + color[1], color[2] + color[2]];
      } else {
        return "rgb(0,0,0)";
      }
      var rgb = hex.map(function (h) {
        return parseInt(h, 16);
      });
      if (opacity !== undefined && opacity !== false) {
        if (Math.abs(opacity) > 1) opacity = 1.0;
        return "rgba(" + rgb.join(",") + "," + opacity + ")";
      } else {
        return "rgb(" + rgb.join(",") + ")";
      }
    }

    function getLineResponsiveClasses(settings, position) {
      var responsiveClasses = [];
      var positionKey = position.replace(/-/g, "_");

      if (settings["pxl_container_line_hide_laptop_" + positionKey]) {
        responsiveClasses.push("pxl-hide-laptop");
      }
      if (settings["pxl_container_line_hide_tablet_landscape_" + positionKey]) {
        responsiveClasses.push("pxl-hide-tablet-landscape");
      }
      if (settings["pxl_container_line_hide_tablet_portrait_" + positionKey]) {
        responsiveClasses.push("pxl-hide-tablet-portrait");
      }
      if (settings["pxl_container_line_hide_mobile_landscape_" + positionKey]) {
        responsiveClasses.push("pxl-hide-mobile-landscape");
      }
      if (settings["pxl_container_line_hide_mobile_portrait_" + positionKey]) {
        responsiveClasses.push("pxl-hide-mobile-portrait");
      }

      return responsiveClasses.length > 0 ? " " + responsiveClasses.join(" ") : "";
    }

    function getResponsiveLineStyles(settings, isVertical) {
      var laptopStyle = settings.pxl_container_line_s_laptop || "gr-df";
      var laptopStyleLr = settings.pxl_container_line_srl_laptop || "gr-df";
      var laptopColor = settings.pxl_container_line_c_laptop || "";

      var tabletLandscapeStyle =
        settings.pxl_container_line_s_tablet_landscape &&
        settings.pxl_container_line_s_tablet_landscape !== "inherit"
          ? settings.pxl_container_line_s_tablet_landscape
          : laptopStyle;
      var tabletLandscapeStyleLr =
        settings.pxl_container_line_srl_tablet_landscape &&
        settings.pxl_container_line_srl_tablet_landscape !== "inherit"
          ? settings.pxl_container_line_srl_tablet_landscape
          : laptopStyleLr;
      var tabletLandscapeColor = settings.pxl_container_line_c_tablet_landscape || laptopColor;

      var tabletPortraitStyle =
        settings.pxl_container_line_s_tablet_portrait &&
        settings.pxl_container_line_s_tablet_portrait !== "inherit"
          ? settings.pxl_container_line_s_tablet_portrait
          : tabletLandscapeStyle;
      var tabletPortraitStyleLr =
        settings.pxl_container_line_srl_tablet_portrait &&
        settings.pxl_container_line_srl_tablet_portrait !== "inherit"
          ? settings.pxl_container_line_srl_tablet_portrait
          : tabletLandscapeStyleLr;
      var tabletPortraitColor =
        settings.pxl_container_line_c_tablet_portrait || tabletLandscapeColor;

      var mobileLandscapeStyle =
        settings.pxl_container_line_s_mobile_landscape &&
        settings.pxl_container_line_s_mobile_landscape !== "inherit"
          ? settings.pxl_container_line_s_mobile_landscape
          : tabletPortraitStyle;
      var mobileLandscapeStyleLr =
        settings.pxl_container_line_srl_mobile_landscape &&
        settings.pxl_container_line_srl_mobile_landscape !== "inherit"
          ? settings.pxl_container_line_srl_mobile_landscape
          : tabletPortraitStyleLr;
      var mobileLandscapeColor =
        settings.pxl_container_line_c_mobile_landscape || tabletPortraitColor;

      var mobilePortraitStyle =
        settings.pxl_container_line_s_mobile_portrait &&
        settings.pxl_container_line_s_mobile_portrait !== "inherit"
          ? settings.pxl_container_line_s_mobile_portrait
          : mobileLandscapeStyle;
      var mobilePortraitStyleLr =
        settings.pxl_container_line_srl_mobile_portrait &&
        settings.pxl_container_line_srl_mobile_portrait !== "inherit"
          ? settings.pxl_container_line_srl_mobile_portrait
          : mobileLandscapeStyleLr;
      var mobilePortraitColor =
        settings.pxl_container_line_c_mobile_portrait || mobileLandscapeColor;

      return {
        laptop: { style: isVertical ? laptopStyle : laptopStyleLr, color: laptopColor },
        tablet_landscape: {
          style: isVertical ? tabletLandscapeStyle : tabletLandscapeStyleLr,
          color: tabletLandscapeColor
        },
        tablet_portrait: {
          style: isVertical ? tabletPortraitStyle : tabletPortraitStyleLr,
          color: tabletPortraitColor
        },
        mobile_landscape: {
          style: isVertical ? mobileLandscapeStyle : mobileLandscapeStyleLr,
          color: mobileLandscapeColor
        },
        mobile_portrait: {
          style: isVertical ? mobilePortraitStyle : mobilePortraitStyleLr,
          color: mobilePortraitColor
        }
      };
    }

    function generateLineCss(style, color, isVertical) {
      if (!color) return "";

      if (isVertical) {
        switch (style) {
          case "gr-btt":
            return (
              "background: linear-gradient(180deg, " +
              hex2rgba(color, 0) +
              " 0%, " +
              hex2rgba(color, 0.88) +
              " 50%, " +
              color +
              " 100%);"
            );
          case "gr-ttb":
            return (
              "background: linear-gradient(180deg, " +
              color +
              " 0%, " +
              hex2rgba(color, 0.88) +
              " 50%, " +
              hex2rgba(color, 0) +
              " 100%);"
            );
          case "gr-df":
            return (
              "background: linear-gradient(180deg, " +
              hex2rgba(color, 0) +
              " 0%, " +
              color +
              " 25%, " +
              color +
              " 50%, " +
              color +
              " 75%, " +
              hex2rgba(color, 0) +
              " 100%);"
            );
          case "gr-df-bold":
            return (
              "background: linear-gradient(180deg, " +
              hex2rgba(color, 0) +
              " 0%, " +
              hex2rgba(color, 0.8) +
              " 25%, " +
              color +
              " 50%, " +
              hex2rgba(color, 0.8) +
              " 75%, " +
              hex2rgba(color, 0) +
              " 100%);"
            );
          case "clr":
            return "background: " + color + ";";
          default:
            return "background: " + color + ";";
        }
      } else {
        switch (style) {
          case "gr-rtl":
            return (
              "background: linear-gradient(90deg, " +
              color +
              " 0%, " +
              hex2rgba(color, 0.88) +
              " 50%, " +
              hex2rgba(color, 0) +
              " 100%);"
            );
          case "gr-ltr":
            return (
              "background: linear-gradient(90deg, " +
              hex2rgba(color, 0) +
              " 0%, " +
              hex2rgba(color, 0.88) +
              " 50%, " +
              color +
              " 100%);"
            );
          case "gr-df":
            return (
              "background: linear-gradient(90deg, " +
              hex2rgba(color, 0) +
              " 0%, " +
              hex2rgba(color, 0.13) +
              " 25%, " +
              hex2rgba(color, 0.16) +
              " 50%, " +
              hex2rgba(color, 0.13) +
              " 75%, " +
              hex2rgba(color, 0) +
              " 100%);"
            );
          case "gr-df-bold":
            return (
              "background: linear-gradient(90deg, " +
              hex2rgba(color, 0) +
              " 0%, " +
              hex2rgba(color, 0.8) +
              " 25%, " +
              hex2rgba(color, 1) +
              " 50%, " +
              hex2rgba(color, 0.8) +
              " 75%, " +
              hex2rgba(color, 0) +
              " 100%);"
            );
          case "clr":
            return "background: " + color + ";";
          default:
            return "background: " + color + ";";
        }
      }
    }

    // Check if filter already exists to prevent duplicate registration
    var filterExists = false;
    try {
      var filters = _elementor.hooks.filters || {};
      if (filters["pxl_element_container/before-render"]) {
        filterExists = true;
      }
    } catch (e) {
      // Continue if check fails
    }

    if (!filterExists) {
      _elementor.hooks.addFilter(
        "pxl_element_container/before-render",
        function (html, settings, el) {
          /** -----------------------
           * Border
           * ---------------------- */
          if (
            typeof settings.pxl_container_border_options !== "undefined" &&
            Array.isArray(settings.pxl_container_border_options) &&
            settings.pxl_container_border_options.length > 0
          ) {
            var sides = settings.pxl_container_border_options;
            var style = settings.pxl_container_border_style || "1";
            var styleClass = Array.isArray(style) ? style.join("-") : style;

            html +=
              '<div class="pxl-container-border pxl-container-border__style-' + styleClass + '">';
            sides.forEach(function (side) {
              html +=
                '<span class="pxl-container-border__item pxl-container-border__item-' +
                side +
                '"></span>';
            });
            html += "</div>";
          }

          /** -----------------------
           * Line (responsive)
           * ---------------------- */
          if (
            typeof settings.pxl_container_line_o !== "undefined" &&
            Array.isArray(settings.pxl_container_line_o) &&
            settings.pxl_container_line_o.length > 0
          ) {
            var linePositions = settings.pxl_container_line_o;

            linePositions.forEach(function (item) {
              var responsiveClassString = getLineResponsiveClasses(settings, item);
              var isVertical =
                item === "left" || item === "right" || item.indexOf("vertical") !== -1;

              var responsiveStyles = getResponsiveLineStyles(settings, isVertical);

              var laptopCss = generateLineCss(
                responsiveStyles.laptop.style,
                responsiveStyles.laptop.color,
                isVertical
              );
              var tabletLandscapeCss = generateLineCss(
                responsiveStyles.tablet_landscape.style,
                responsiveStyles.tablet_landscape.color,
                isVertical
              );
              var tabletPortraitCss = generateLineCss(
                responsiveStyles.tablet_portrait.style,
                responsiveStyles.tablet_portrait.color,
                isVertical
              );
              var mobileLandscapeCss = generateLineCss(
                responsiveStyles.mobile_landscape.style,
                responsiveStyles.mobile_landscape.color,
                isVertical
              );
              var mobilePortraitCss = generateLineCss(
                responsiveStyles.mobile_portrait.style,
                responsiveStyles.mobile_portrait.color,
                isVertical
              );

              html +=
                '<span class="pxl-line pxl-line__' +
                item +
                responsiveClassString +
                '" ' +
                'data-laptop-style="' +
                laptopCss +
                '" ' +
                'data-tablet-landscape-style="' +
                tabletLandscapeCss +
                '" ' +
                'data-tablet-portrait-style="' +
                tabletPortraitCss +
                '" ' +
                'data-mobile-landscape-style="' +
                mobileLandscapeCss +
                '" ' +
                'data-mobile-portrait-style="' +
                mobilePortraitCss +
                '" ' +
                'style="' +
                laptopCss +
                '"></span>';
            });
          }

          /** -----------------------
           * Overlay sides
           * ---------------------- */
          if (
            typeof settings.pxl_container_overlay_sides !== "undefined" &&
            Array.isArray(settings.pxl_container_overlay_sides) &&
            settings.pxl_container_overlay_sides.length > 0
          ) {
            var sides = settings.pxl_container_overlay_sides;
            var type = settings.pxl_container_overlay_type || "default";

            sides.forEach(function (side) {
              html +=
                '<span class="pxl-container-overlay__item pxl-container-overlay__item-' +
                side +
                " pxl-container-overlay__item-" +
                type +
                '"></span>';
            });
          }

          /** -----------------------
           * Dots
           * ---------------------- */
          if (
            typeof settings.pxl_dot_container_pos !== "undefined" &&
            Array.isArray(settings.pxl_dot_container_pos) &&
            settings.pxl_dot_container_pos.length > 0
          ) {
            var items = settings.pxl_dot_container_pos;
            var type = settings.pxl_dot_container_type || "default";

            items.forEach(function (item) {
              html += '<span class="pxl-dot pxl-dot__' + item + " pxl-dot__" + type + '"></span>';
            });
          }

          /** -----------------------
           * Link overlay
           * ---------------------- */
          if (
            typeof settings.pxl_container_link !== "undefined" &&
            settings.pxl_container_link &&
            settings.pxl_container_link.url
          ) {
            var linkAttributes = [];

            if (settings.pxl_container_link.url) {
              linkAttributes.push('href="' + settings.pxl_container_link.url + '"');
            }
            if (settings.pxl_container_link.is_external) {
              linkAttributes.push('target="_blank"');
            }
            if (settings.pxl_container_link.nofollow) {
              linkAttributes.push('rel="nofollow"');
            }

            var attributesString = linkAttributes.join(" ");
            html += "<a " + attributesString + ' class="pxl-container pxl-container__link"></a>';
          }

          /** -----------------------
           * Star & Light
           * ---------------------- */
          function addStarAndLight() {
            if (
              settings.pxl_container_star_color_option === "yes" &&
              settings.pxl_container_star_color
            ) {
              const w = (settings.pxl_container_star_width || {}).size ?? null;
              const wu = (settings.pxl_container_star_width || {}).unit ?? "px";
              const h = (settings.pxl_container_star_height || {}).size ?? null;
              const hu = (settings.pxl_container_star_height || {}).unit ?? "px";
              const n = settings.pxl_container_star_number ?? 60;

              let starStyle = "";
              if (Number.isFinite(w)) starStyle += `width:${w}${wu};`;
              if (Number.isFinite(h)) starStyle += `height:${h}${hu};`;

              html +=
                `<canvas class="pxl-star"` +
                ` data-color="${settings.pxl_container_star_color}"` +
                ` data-star="${n}"` +
                ` style="${starStyle}"></canvas>`;
            }

            if (
              settings.pxl_container_light_color_option === "yes" &&
              settings.pxl_container_light_color
            ) {
              const w =
                (settings.pxl_container_light_width || {}).size ??
                (settings.pxl_container_star_width || {}).size ??
                null;
              const wu =
                (settings.pxl_container_light_width || {}).unit ??
                (settings.pxl_container_star_width || {}).unit ??
                "px";
              const h =
                (settings.pxl_container_light_height || {}).size ??
                (settings.pxl_container_star_height || {}).size ??
                null;
              const hu =
                (settings.pxl_container_light_height || {}).unit ??
                (settings.pxl_container_star_height || {}).unit ??
                "px";

              const blurVal = (settings.pxl_container_light_blur || {}).size ?? 0;
              const opacityRaw = (settings.pxl_container_light_opacity || {}).size ?? 100;
              const opacity = Math.max(0, Math.min(+opacityRaw / 100, 1)); // 0 → 1

              let lightStyle = "";
              if (Number.isFinite(w)) lightStyle += `width:${w}${wu};`;
              if (Number.isFinite(h)) lightStyle += `height:${h}${hu};`;
              lightStyle += `background:${settings.pxl_container_light_color};`;
              lightStyle += `opacity:${opacity};`;
              if (blurVal > 0) lightStyle += `filter:blur(${blurVal}px);`;

              // (kept intentionally if needed later) render star canvas async
              clearTimeout(window.pxlRenderStarTimeout);
              window.pxlRenderStarTimeout = setTimeout(() => {
                const canvas = document.createElement("canvas");
                canvas.className = "pxl-star";
                canvas.setAttribute("data-color", settings.pxl_container_star_color);
                const n = settings.pxl_container_star_number ?? 60;
                canvas.setAttribute("data-star", n);
                document.querySelector(".elementor-element-" + settings._id)?.appendChild(canvas);
              }, 2000);
            }
          }
          addStarAndLight();

          return html;
        }
      );
    }

    // Check if action already exists to prevent duplicate registration
    var actionExists = false;
    try {
      var actions = _elementor.hooks.actions || {};
      if (actions["frontend/element_ready/container"]) {
        actionExists = true;
      }
    } catch (e) {
      // Continue if check fails
    }

    if (!actionExists) {
      _elementor.hooks.addAction("frontend/element_ready/container", function ($scope) {
        applyResponsiveLineStyles();
        addResizeListener();
        initStickyContainer($scope);
      });
    }

    brighthubHooksRegistered = true;
  }

  /**
   * =========================
   * MODULE 3: Sticky Container + Responsive Line Styles
   * =========================
   */
  function applyResponsiveLineStyles() {
    // Debounce to prevent excessive calls
    if (brighthubResizeTimeout) {
      clearTimeout(brighthubResizeTimeout);
    }

    brighthubResizeTimeout = setTimeout(function () {
      const lines = document.querySelectorAll(".pxl-line[data-laptop-style]");
      const windowWidth = window.innerWidth;

      lines.forEach((line) => {
        let targetStyle = "";
        if (windowWidth <= 575) {
          targetStyle =
            line.getAttribute("data-mobile-portrait-style") ||
            line.getAttribute("data-mobile-landscape-style") ||
            line.getAttribute("data-tablet-portrait-style") ||
            line.getAttribute("data-tablet-landscape-style") ||
            line.getAttribute("data-laptop-style");
        } else if (windowWidth <= 767) {
          targetStyle =
            line.getAttribute("data-mobile-landscape-style") ||
            line.getAttribute("data-tablet-portrait-style") ||
            line.getAttribute("data-tablet-landscape-style") ||
            line.getAttribute("data-laptop-style");
        } else if (windowWidth <= 1024) {
          targetStyle =
            line.getAttribute("data-tablet-portrait-style") ||
            line.getAttribute("data-tablet-landscape-style") ||
            line.getAttribute("data-laptop-style");
        } else if (windowWidth <= 1366) {
          targetStyle =
            line.getAttribute("data-tablet-landscape-style") ||
            line.getAttribute("data-laptop-style");
        } else {
          targetStyle = line.getAttribute("data-laptop-style");
        }

        if (targetStyle) {
          const currentStyles = line.style.cssText.replace(/background[^;]*;?/g, "").trim();
          line.style.cssText = targetStyle + (currentStyles ? "; " + currentStyles : "");
        }
      });
    }, 100);
  }

  // Add resize listener for responsive line styles (only once)
  var resizeListenerAdded = false;
  function addResizeListener() {
    if (resizeListenerAdded) return;
    resizeListenerAdded = true;
    $(window).on("resize.brighthubResponsiveLines", function () {
      applyResponsiveLineStyles();
    });
  }

  function initStickyContainer($container) {
    if (!$container.hasClass("pxl-sticky-container") || !$container.data("pxl-sticky")) return;

    // Prevent multiple initialization on the same container
    if ($container.data("pxl-sticky-initialized")) return;
    $container.data("pxl-sticky-initialized", true);

    var stickyOffset = parseInt($container.data("sticky-offset"));
    var stickyMode = $container.data("sticky-mode");
    var triggerPosition = parseInt($container.data("trigger-position"));
    var hideOnFooter = parseInt($container.data("ft-hide"));

    var originalCss = {
      position: $container.css("position"),
      top: stickyMode === "top" ? $container.css("top") : "unset",
      bottom: stickyMode === "bottom" ? $container.css("bottom") : "unset",
      left: $container.css("left"),
      width: $container.css("width"),
      zIndex: $container.css("z-index")
    };

    var $footer = $(".pxl-footer-show");
    var footerTop = $footer.length ? $footer.offset().top : 0;
    var scrollAnimationFrame = null;

    function handleScroll() {
      // Debounce scroll handler
      if (scrollAnimationFrame) {
        cancelAnimationFrame(scrollAnimationFrame);
      }

      scrollAnimationFrame = requestAnimationFrame(function () {
        scrollAnimationFrame = null;
        var scrollTop = $(window).scrollTop();
        var windowHeight = $(window).height();

        if (scrollTop >= triggerPosition) {
          if (footerTop && scrollTop + windowHeight >= footerTop - hideOnFooter) {
            if ($container.hasClass("pxl-sticky-active")) {
              var resetCss = { ...originalCss };
              if (stickyMode === "top") {
                delete resetCss.bottom;
              } else {
                delete resetCss.top;
              }
              $container.removeClass("pxl-sticky-active").css(resetCss);
            }
          } else {
            if (!$container.hasClass("pxl-sticky-active")) {
              var containerWidth = $container.outerWidth();
              var containerLeft = $container.offset().left;

              var cssProps = {
                position: "fixed",
                left: containerLeft + "px",
                width: containerWidth + "px",
                zIndex: 1000
              };

              if (stickyMode === "top") {
                cssProps.top = stickyOffset + "px";
                cssProps.bottom = "unset";
              } else {
                cssProps.bottom = stickyOffset + "px";
                cssProps.top = "unset";
              }

              $container.addClass("pxl-sticky-active").css(cssProps);
            }
          }
        } else {
          if ($container.hasClass("pxl-sticky-active")) {
            $container.removeClass("pxl-sticky-active").css(originalCss);
          }
        }
      });
    }

    // Use namespaced event to allow cleanup
    $(window).on("scroll.brighthubSticky", handleScroll);

    // Debounce resize handler
    var resizeTimeout;
    $(window).on("resize.brighthubSticky", function () {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(function () {
        if ($container.hasClass("pxl-sticky-active")) {
          var newWidth = $container.parent().width();
          var newLeft = $container.parent().offset().left;
          $container.css({
            width: newWidth + "px",
            left: newLeft + "px"
          });
        }
        if ($footer.length) {
          footerTop = $footer.offset().top;
        }
      }, 150);
    });
  }

  /**
   * =========================
   * MODULE 5: Init (Custom CSS modules removed)
   * =========================
   */
  function initCustomFunctions() {
    // Prevent multiple initialization
    if (brighthubCustomControlsInitialized) return;

    try {
      if (typeof brighthub_section_start_render === "function") {
        brighthub_section_start_render();
        brighthubCustomControlsInitialized = true;
      }
    } catch (error) {
      console.error("Error initializing custom functions:", error);
    }
  }

  // === Register init ===
  // Use one-time event handler to prevent multiple registrations
  var elementorInitHandler = function () {
    initCustomFunctions();
    // Remove handler after first execution
    $(window).off("elementor/frontend/init", elementorInitHandler);
  };

  $(window).on("elementor/frontend/init", elementorInitHandler);

  // Fallback initialization with debounce
  var docReadyTimeout;
  $(document).ready(function () {
    clearTimeout(docReadyTimeout);
    docReadyTimeout = setTimeout(function () {
      if (typeof elementor !== "undefined" && !brighthubCustomControlsInitialized) {
        initCustomFunctions();
      }
    }, 1000);
  });
})(jQuery);
