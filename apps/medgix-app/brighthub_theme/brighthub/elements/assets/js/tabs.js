(function ($) {
  "use strict";

  function fixAccordions() {
    $(".pxl-accordion").each(function () {
      var $accordion = $(this);
      $accordion.find(".pxl-accordion__item").removeClass("active");
      $accordion.find(".pxl-accordion__item-content").hide();
      var $firstItem = $accordion.find(".pxl-accordion__item:first-child");
      $firstItem.addClass("active");
      $firstItem.find(".pxl-accordion__item-content").show();
    });
  }

  function setupAccordionClicks() {
    $(".pxl-accordion__item-title").off("click");
    $(".pxl-accordion__item-title").on("click", function (e) {
      e.preventDefault();
      var $this = $(this);
      var $parent = $this.parent(".pxl-accordion__item");
      var $content = $parent.find(".pxl-accordion__item-content");
      var $accordion = $parent.parent(".pxl-accordion");

      if ($parent.hasClass("active")) {
        $parent.removeClass("active");
        $content.slideUp(400);
        if ($(this).parent().index() > 0) {
          $parent.prev().removeClass("active-prev");
          $parent.next().removeClass("active-next");
        }
      } else {
        $accordion.find(".pxl-accordion__item").removeClass("active active-prev active-next");
        $accordion.find(".pxl-accordion__item-content").slideUp(400);
        $parent.addClass("active");
        $content.slideDown(400);
        if ($(this).parent().index() > 0) {
          $parent.prev().addClass("active-prev");
          $parent.next().addClass("active-next");
        }
      }
    });
  }

  var pxl_widget_tabs_handler = function ($scope, $) {
    $scope.find(".pxl-tabs.tab-effect-slide .pxl-tabs__list-item").on("click", function (e) {
      e.preventDefault();
      var target = $(this).data("target");
      var parent = $(this).parents(".pxl-tabs");
      parent.find(".pxl-tabs__content>.pxl-tabs__item").slideUp(400);
      parent.find(".pxl-tabs__list .pxl-tabs__list-item").removeClass("active");
      $(this).addClass("active");
      $(target).slideDown(400);
    });

    $scope.find(".pxl-tabs.tab-effect-fade .pxl-tabs__list-item").on("click", function (e) {
      e.preventDefault();
      var target = $(this).data("target");
      var parent = $(this).parents(".pxl-tabs");
      parent.find(".pxl-tabs__content>.pxl-tabs__item").hide();
      parent.find(".pxl-tabs__list .pxl-tabs__list-item").removeClass("active");
      $(this).addClass("active");
      $(target).show();
    });

    $scope.find(".pxl-tabs").each(function () {
      var $tabs = $(this);
      var allTab = $tabs.find(".pxl-tabs__item-all");
      var tab_btn = $tabs.find(".pxl-tabs__list");

      if (allTab.length > 0) {
        var allTabStructure = allTab.html();
        allTab.empty();

        var secondTab = $tabs.find(".pxl-tabs__item:not(.pxl-tabs__item-all)").eq(0);
        if (secondTab.length > 0) {
          secondTab.children().clone(true).appendTo(allTab);
        }

        var allTab_child = allTab.find(".elementor-widget-container > div");

        if (allTab_child.length > 0) {
          $tabs
            .find(".pxl-tabs__item:not(.pxl-tabs__item-all)")
            .slice(1)
            .each(function () {
              $(this)
                .find(".elementor-widget-container > div > div")
                .each(function () {
                  $(this).clone(true).appendTo(allTab_child);
                });
            });
        }

        var loadmore = $tabs.find(".pxl-tabs__faqs-loadmore");
        if (loadmore.length > 0) {
          var accordion = allTab.find(".pxl-accordion");
          accordion.append(loadmore);
          var itemsToShow = parseInt(allTab.data("show")) || 6;
          var accordionItems = accordion.find(".pxl-accordion__item");
          if (accordionItems.length > itemsToShow) {
            accordionItems.slice(itemsToShow).hide();
            loadmore.show();

            loadmore
              .find(".pxl-tabs__loadmore")
              .off("click")
              .on("click", function (e) {
                e.preventDefault();

                var $btn = $(this).find(".pxl-tabs__loadmore");
                var $loadingText = $btn.data("loading-text") || "Loading";
                var $originalText = $btn.text();
                $btn.find("span").text($loadingText);
                $btn.addClass("loading");

                var $hiddenItems = accordion.find(".pxl-accordion__item:hidden");

                setTimeout(function () {
                  $hiddenItems.slice(0, itemsToShow).fadeIn(0);
                  $btn.removeClass("loading");
                  if ($hiddenItems.length <= itemsToShow) {
                    $btn.parent().remove();
                    setTimeout(function () {
                      loadmore.fadeOut();
                    }, 3000);
                  } else {
                    $btn.find("span").text($originalText);
                  }
                }, 0);
              });
          } else {
            loadmore.hide();
          }
        }

        function resetAccordionStates() {
          var $accordions = allTab.find(".pxl-accordion");
          $accordions.each(function () {
            var $accordion = $(this);

            $accordion
              .find(".pxl-accordion__item")
              .removeClass("active")
              .find(".pxl-accordion__item-content")
              .hide();

            $accordion
              .find(".pxl-accordion__item:first-child")
              .addClass("active")
              .find(".pxl-accordion__item-content")
              .show();
          });
        }

        resetAccordionStates();
      }

      if (tab_btn.data("count") === "yes") {
        $tabs.find(".pxl-tabs__list-item").each(function () {
          const item_idx = $(this).index();
          const item_count = $tabs
            .find(".pxl-tabs__content .pxl-tabs__item")
            .eq(item_idx)
            .find(".pxl-accordion__item").length;
          $(this).append(`<span class="item-count">(${item_count})</span>`);
        });
      }
    });
  };

  var pxl_widget_accordion_handler = function ($scope, $) {
    $scope.find(".pxl-accordion").each(function () {
      var $accordion = $(this);

      $accordion.find(".pxl-accordion__item").removeClass("active");
      $accordion.find(".pxl-accordion__item-content").hide();

      $accordion
        .find(".pxl-accordion__item:first-child")
        .addClass("active")
        .find(".pxl-accordion__item-content")
        .show();
    });

    $scope
      .find(".pxl-accordion__item-title")
      .off("click")
      .on("click", function (e) {
        e.preventDefault();

        var $this = $(this);
        var $parent = $this.parent(".pxl-accordion__item");
        var $content = $parent.find(".pxl-accordion__item-content");
        var $accordion = $parent.parent(".pxl-accordion");

        if ($parent.hasClass("active")) {
          $parent.removeClass("active");
          $content.slideUp(400);
        } else {
          $accordion.find(".pxl-accordion__item").removeClass("active");
          $accordion.find(".pxl-accordion__item-content").slideUp(400);

          $parent.addClass("active");
          $content.slideDown(400);
        }
      });
  };

  $(function () {
    fixAccordions();
    setupAccordionClicks();
  });

  $(document).ready(function () {
    fixAccordions();
    setupAccordionClicks();

    $(".pxl-tabs").each(function () {
      var $tabs = $(this);

      $tabs.find("#toggleTab").on("change", function () {
        let $switchBtn = $(this).closest(".pxl-tabs__switch-btn");
        let $tabsContent = $tabs.find(".pxl-tabs__content");
        let $tabItems = $tabsContent.find(".pxl-tabs__item");

        let $dataTab1 = $switchBtn.data("st");
        let $dataTab2 = $switchBtn.data("nd");

        $tabItems.toggleClass("active");
        $tabItems.filter(".active").prependTo($tabsContent);
        if ($(this).is(":checked")) {
          $tabs.find(".pxl-tabs__switch-txt").text($dataTab2);
        } else {
          $tabs.find(".pxl-tabs__switch-txt").text($dataTab1);
        }
      });

      pxl_widget_tabs_handler(
        {
          find: function (selector) {
            return $tabs.find(selector);
          }
        },
        $
      );
    });
  });

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/pxl_tabs.default",
      pxl_widget_tabs_handler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/pxl_banner_tabs.default",
      pxl_widget_tabs_handler
    );

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/pxl_accordion.default",
      pxl_widget_accordion_handler
    );

    elementorFrontend.hooks.addAction("frontend/element_ready/global", function () {
      fixAccordions();
      setupAccordionClicks();
    });
  });

  $(document).ajaxComplete(function () {
    setTimeout(function () {
      fixAccordions();
      setupAccordionClicks();
    }, 100);
  });
})(jQuery);
