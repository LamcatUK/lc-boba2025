// Add your custom JS here.
AOS.init({
  easing: "ease-in",
  once: true,
});

(function () {
  // Hide header on scroll
  var doc = document.documentElement;
  var w = window;

  var prevScroll = w.scrollY || doc.scrollTop;
  var curScroll;
  var direction = 0;
  var prevDirection = 0;

  var header = document.getElementById("wrapper-navbar");

  var checkScroll = function () {
    // Find the direction of scroll (0 - initial, 1 - up, 2 - down)
    curScroll = w.scrollY || doc.scrollTop;
    if (curScroll > prevScroll) {
      // Scrolled down
      direction = 2;
    } else if (curScroll < prevScroll) {
      // Scrolled up
      direction = 1;
    }

    if (direction !== prevDirection) {
      toggleHeader(direction, curScroll);
    }

    prevScroll = curScroll;
  };

  var toggleHeader = function (direction, curScroll) {
    if (direction === 2 && curScroll > 125) {
      // Replace 52 with the height of your header in px
      if (!document.getElementById("navbar").classList.contains("show")) {
        header.classList.add("hide");
        prevDirection = direction;
      }
    } else if (direction === 1) {
      header.classList.remove("hide");
      prevDirection = direction;
    }
  };

  window.addEventListener("scroll", checkScroll);

  // Header background
  document.addEventListener("scroll", function () {
    var nav = document.getElementById("navbar");
    document.querySelectorAll(".dropdown-menu").forEach(function (dropdown) {
      dropdown.classList.remove("show");
    });
    document.querySelectorAll(".dropdown-toggle").forEach(function (toggle) {
      toggle.classList.remove("show");
      toggle.blur();
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    var mainSlider = document.querySelector("#product-gallery");
    var thumbSlider = document.querySelector("#product-thumbnails");

    // Ensure the main slider exists before running anything
    if (mainSlider) {
      var slideCount = mainSlider.querySelectorAll(".splide__slide").length;
      var showArrows = slideCount > 1;

      var main = new Splide(mainSlider, {
        type: "slide", // Prevents looping issues
        perPage: 1,
        pagination: false,
        arrows: showArrows,
        cover: false,
      });

      // If the thumbnail slider also exists, sync it
      if (thumbSlider) {
        var thumbs = new Splide(thumbSlider, {
          fixedWidth: 80,
          fixedHeight: 80,
          isNavigation: true,
          gap: 10,
          focus: 0,
          pagination: false,
          arrows: false,
        });

        main.sync(thumbs);
        thumbs.mount();
      }

      main.mount();
    }
  });
})();
