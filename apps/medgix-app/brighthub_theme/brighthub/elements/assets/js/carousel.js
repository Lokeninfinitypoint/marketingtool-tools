(function ($) {
  "use strict";

  $(window).on("elementor/frontend/init", function () {
    const carouselWidgets = [
      "pxl_post_carousel",
      "pxl_testimonial_carousel",
      "pxl_team_carousel",
      "pxl_partner_carousel",
      "pxl_language_carousel",
      "pxl_image_carousel",
      "pxl_image_box_carousel",
      "pxl_icon_box_carousel",
      "pxl_textbox_carousel"
    ];

    carouselWidgets.forEach((widget) => {
      elementorFrontend.hooks.addAction(
        `frontend/element_ready/${widget}.default`,
        function ($scope) {
          pxl_swiper_handler($scope);
        }
      );
    });
  });

  function pxl_swiper_handler($scope) {
    $scope.find(".pxl-swiper-slider").each(function () {
      var $this = $(this);
      var $container = $this.closest($scope);
      var settings = $this.find(".pxl-swiper-container").data().settings;

      var carousel_settings = {
        direction: settings["slide_direction"],
        effect: settings["slide_mode"],
        wrapperClass: "pxl-swiper-slider__wrapper",
        slideClass: "pxl-swiper-slider__item",
        slidesPerView: settings["slides_to_show"],
        slidesPerGroup: settings["slides_to_scroll"],
        slidesPerColumn: settings["slide_percolumn"],
        spaceBetween: 0,
        observer: true,
        observeParents: true,
        navigation: {
          nextEl: $this.find(".pxl-swiper__nav .pxl-swiper__nav-next")[0],
          prevEl: $this.find(".pxl-swiper__nav .pxl-swiper__nav-prev")[0]
        },
        pagination: {
          type: settings["pagination_type"],
          el: $this.find(".pxl-swiper-dots")[0],
          clickable: true,
          modifierClass: "pxl-swiper-pagination-",
          bulletClass: "swiper-pagination-bullet",
          renderCustom: (swiper, element, current, total) => `${current} of ${total}`,
          renderBullet: (index, className) => `<span class="${className}"></span>`
        },
        speed: settings["speed"],
        watchSlidesProgress: true,
        watchSlidesVisibility: true,
        breakpoints: {
          0: {
            slidesPerView: settings["slides_to_show_xs"],
            slidesPerGroup: settings["slides_to_scroll"]
          },
          576: {
            slidesPerView: settings["slides_to_show_sm"],
            slidesPerGroup: settings["slides_to_scroll"]
          },
          768: {
            slidesPerView: settings["slides_to_show_md"],
            slidesPerGroup: settings["slides_to_scroll"]
          },
          992: {
            slidesPerView: settings["slides_to_show_lg"],
            slidesPerGroup: settings["slides_to_scroll"]
          },
          1200: {
            slidesPerView: settings["slides_to_show"],
            slidesPerGroup: settings["slides_to_scroll"]
          },
          1400: {
            slidesPerView: settings["slides_to_show_xxl"],
            slidesPerGroup: settings["slides_to_scroll"]
          }
        },
        on: {
          init: function (swiper) {
            updateAvatarHighlight(swiper, $container);
          },
          slideChange: function (swiper) {
            updateAvatarHighlight(swiper, $container);
          },
          sliderMove: function (swiper) {
            updateAvatarHighlight(swiper, $container);
          }
        }
      };

      if (settings["center_slide"] === true || settings["center_slide"] === "true")
        carousel_settings.centeredSlides = true;

      if (settings["loop"] === true || settings["loop"] === "true") carousel_settings.loop = true;

      if (settings["autoplay"] === true || settings["autoplay"] === "true") {
        carousel_settings.autoplay = {
          delay: parseInt(settings["delay"], 10) || 3000,
          disableOnInteraction:
            settings["pause_on_interaction"] === true ||
            settings["pause_on_interaction"] === "true",
          reverseDirection:
            settings["reverseDirection"] === true || settings["reverseDirection"] === "true",
          waitForTransition: false
        };
      } else {
        carousel_settings.autoplay = false;
      }

      if (settings["creativeEffect"] === "card_rotate") {
        carousel_settings.effect = "creative";
        carousel_settings.creativeEffect = {
          perspective: true,
          limitProgress: 3,
          prev: {
            translate: ["-100%", "30px", -100],
            rotate: [0, 0, -8],
            origin: "bottom"
          },
          next: {
            translate: ["100%", "30px", -100],
            rotate: [0, 0, 8],
            origin: "bottom"
          }
        };
      }

      if (settings["creativeEffect"] === "card_3d") {
        let raf;
        carousel_settings.effect = "coverflow";
        carousel_settings.centeredSlides = true;
        carousel_settings.coverflowEffect = {
          rotate: 50,
          stretch: 0,
          depth: 60,
          modifier: 1,
          slideShadows: true
        };

        carousel_settings.on = {
          slideChange: function () {
            this.slides.forEach((slide) => {
              if (window.innerWidth > 1200) {
                slide.style.transition = "transform 0.3s ease";
                slide.style.transform = slide.style.transform.replace(/scale\([^)]*\)/g, "");

                if (slide.classList.contains("swiper-slide-active")) {
                  slide.style.transform += " scale(1.1)";
                } else {
                  slide.style.transform += " scale(0.9)";
                }
              }
            });
          },

          setTransition: function (duration) {
            this.slides.forEach((slide) => {
              slide.style.transitionDuration = `${duration}ms`;
            });
          }
        };
        carousel_settings.on = {
          setTranslate: function () {
            cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
              this.slides.forEach((slide) => {
                if (window.innerWidth > 1200) {
                  slide.style.transform = slide.style.transform.replace(/scale\([^)]*\)/g, "");

                  if (slide.classList.contains("swiper-slide-active")) {
                    slide.style.transform += " scale(1.1)";
                  } else {
                    slide.style.transform += " scale(0.9)";
                  }
                }
              });
            });
          }
        };
      }

      if ($this.find(".pxl-swiper-thumbs").length > 0) {
        var thumb_settings = $this.find(".pxl-swiper-thumbs").data().settings;
        var slide_thumbs = new Swiper($this.find(".pxl-swiper-thumbs")[0], {
          effect: thumb_settings["slide_mode"],
          direction: thumb_settings["slide_direction"],
          spaceBetween: 0,
          slidesPerView: thumb_settings["slides_to_show"],
          centeredSlides: false,
          loop: thumb_settings["loop"],
          watchSlidesProgress: true,
          slideToClickedSlide: true
        });
        carousel_settings.thumbs = { swiper: slide_thumbs };
      }

      var swiper = new Swiper($this.find(".pxl-swiper-container")[0], carousel_settings);

      $container
        .find(".testimonial-3 .pxl-swiper-slider__avatar")
        .off("click.testimonial")
        .on("click.testimonial", function () {
          var index = $(this).data("index");
          swiper.slideToLoop(index);
        });

      if (settings["autoplay"] === "true" && settings["pause_on_hover"] === "true") {
        $($this.find(".pxl-swiper-container")).on({
          mouseenter: function () {
            this.swiper.autoplay.stop();
          },
          mouseleave: function () {
            this.swiper.autoplay.start();
          }
        });
      }

      function updateAvatarHighlight(swiper, $container) {
        var indexToHighlight = swiper.loopedSlides ? swiper.realIndex : swiper.activeIndex;
        var $avatars = $container.find(".pxl-swiper-slider__avatar");
        var avatarCount = $avatars.length;

        $avatars.removeClass("active-1 active-2 active active-4 active-5");

        function getWrappedIndex(index) {
          return (index + avatarCount) % avatarCount;
        }

        $avatars.eq(getWrappedIndex(indexToHighlight)).addClass("active");
        $avatars.eq(getWrappedIndex(indexToHighlight + 1)).addClass("active-4");
        $avatars.eq(getWrappedIndex(indexToHighlight - 1)).addClass("active-2");
        $avatars.eq(getWrappedIndex(indexToHighlight + 2)).addClass("active-5");
        $avatars.eq(getWrappedIndex(indexToHighlight - 2)).addClass("active-1");
      }
    });
  }
})(jQuery);
