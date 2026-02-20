(function ($) {
  "use strict";

  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);

  window.__pxlZoomSTs = window.__pxlZoomSTs || [];

  function killZoomTriggers() {
    if (!window.__pxlZoomSTs.length) return;
    window.__pxlZoomSTs.forEach((st) => {
      try {
        st.kill(true);
      } catch (e) {}
    });
    window.__pxlZoomSTs = [];
  }

  function toNumber(v, def = 0) {
    if (v == null || v === "") return def;
    const n = parseFloat(String(v).replace(/[^\-\d.]/g, ""));
    return Number.isFinite(n) ? n : def;
  }
  function parsePx(v, def = 0) {
    return toNumber(v, def);
  }

  function parseScale(section) {
    const ds = section.dataset.zoomScale;
    if (ds != null && ds !== "") return toNumber(ds, 0.15);
    const legacy = section.dataset.zoomStart;
    if (legacy != null && legacy !== "") return toNumber(legacy, 15) / 100;
    return 0.15;
  }

  function screenPos(section, key, fb) {
    const v = section.dataset[key];
    const pct = v != null && v !== "" ? toNumber(v, fb) : fb;
    return `top ${pct}%`;
  }

  function safeAnimFromData(el) {
    const attr = el.getAttribute("data-settings");
    if (!attr) return null;
    try {
      const st = JSON.parse(attr);
      return st && (st._animation || st.animation) ? st._animation || st.animation : null;
    } catch (e) {
      return null;
    }
  }
  function getElAnimName(el) {
    const fromData = safeAnimFromData(el);
    if (fromData) return fromData;
    const c = Array.from(el.classList).find((cl) => cl.startsWith("e-animations-"));
    return c ? c.replace("e-animations-", "") : null;
  }
  function playElementorEntrance(el, name) {
    el.classList.remove("elementor-invisible");
    if (!name) return;
    if (!el.classList.contains("e-animated")) el.classList.add("e-animated");
    const cls = `e-animations-${name}`;
    if (!el.classList.contains(cls)) el.classList.add(cls);
  }

  function initZoomScroll() {
    killZoomTriggers();

    const prefersReduce =
      window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    document.querySelectorAll(".scroll-item-zoom").forEach((section) => {
      const minW = toNumber(section.dataset.zoomMin, 0);
      const maxW = toNumber(section.dataset.zoomMax, Infinity);
      const w = window.innerWidth;
      if (w < minW && w > maxW) return;

      if (prefersReduce && section.dataset.zoomIgnorePrm !== "1") return;

      const scaleStart = parseScale(section);
      const yStart = parsePx(section.dataset.zoomTop, 20);
      const startPos = screenPos(section, "zoomStartScreen", 80);
      const endPos = screenPos(section, "zoomEndScreen", 0);
      const showMarkers = !!section.dataset.zoomDebug;

      const origin =
        section.dataset.zoomOrigin && section.dataset.zoomOrigin.trim()
          ? section.dataset.zoomOrigin.trim()
          : "top center";

      let trigger = section;
      if (section.dataset.zoomTrigger) {
        const t =
          section.closest(section.dataset.zoomTrigger) ||
          section.querySelector(section.dataset.zoomTrigger);
        if (t) trigger = t;
      }

      section.style.transformOrigin = origin;
      section.style.willChange = "transform";

      const tween = gsap.fromTo(
        section,
        { scale: scaleStart, y: yStart },
        {
          scale: 1,
          y: 0,
          ease: "none",
          scrollTrigger: {
            trigger: trigger,
            start: startPos,
            end: endPos,
            scrub: 0,
            invalidateOnRefresh: true,
            markers: showMarkers,

            // 👉 Thêm toggle class "zoomed" khi kích hoạt / rời vùng trigger
            onToggle(self) {
              section.classList.toggle("zoomed", self.isActive);
            }
          }
        }
      );

      if (tween && tween.scrollTrigger) window.__pxlZoomSTs.push(tween.scrollTrigger);

      // Tự kích hoạt entrance animations của Elementor khi progress ~100%
      const targets = Array.from(section.querySelectorAll(":scope .elementor-element")).filter(
        (el) => {
          if (el === section) return false;
          return !!(
            safeAnimFromData(el) ||
            Array.from(el.classList).some((cl) => cl.startsWith("e-animations-")) ||
            el.classList.contains("elementor-invisible")
          );
        }
      );

      let played = false;
      const stPlay = ScrollTrigger.create({
        trigger: trigger,
        start: startPos,
        end: endPos,
        onUpdate(self) {
          if (!played && self.progress >= 0.99) {
            played = true;
            targets.forEach((el) => {
              if (el.dataset.pxlPlayed === "1") return;
              el.dataset.pxlPlayed = "1";
              playElementorEntrance(el, getElAnimName(el));
            });
          }
        }
      });
      window.__pxlZoomSTs.push(stPlay);
    });

    ScrollTrigger.refresh();
  }

  if (document.readyState === "complete") {
    initZoomScroll();
  } else {
    $(window).on("load", initZoomScroll);
  }

  $(window).on("resize", function () {
    clearTimeout(window.__pxlZoomResizeTimeout);
    window.__pxlZoomResizeTimeout = setTimeout(initZoomScroll, 200);
  });
})(jQuery);
