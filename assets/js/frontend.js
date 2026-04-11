(function ($) {
  // Scroll to filter section when arriving via URL parameter
  $(function () {
    if (new URL(window.location).searchParams.has("product_category")) {
      var el = document.getElementById("invento-catalog-filter");
      if (el) {
        el.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }

    // Ensure video overlay exists on body (for shortcode galleries outside single-product.php)
    if ($(".invento-sc-gallery-thumb-video").length && !$(".invento-video-overlay").length) {
      $("body").append(
        '<div class="invento-video-overlay" aria-hidden="true">' +
        '<div class="invento-video-modal" role="dialog" aria-modal="true">' +
        '<button type="button" class="invento-video-close" aria-label="Close video">&times;</button>' +
        '<div class="invento-video-player"></div>' +
        '</div></div>'
      );
    }
  });

  function parseYouTubeId(url) {
    var match = url.match(
      /(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/,
    );
    return match ? match[1] : "";
  }

  function parseVimeoId(url) {
    var match = url.match(/vimeo\.com\/(?:video\/)?([0-9]+)/);
    return match ? match[1] : "";
  }

  function buildPlayer(type, url) {
    if (type === "self_hosted") {
      return (
        '<video class=\"invento-video\" controls playsinline src=\"' +
        url +
        '\"></video>'
      );
    }

    if (type === "youtube") {
      var id = parseYouTubeId(url);
      if (!id) return "";
      return (
        '<iframe class=\"invento-video\" src=\"https://www.youtube.com/embed/' +
        id +
        '?autoplay=1\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>'
      );
    }

    if (type === "vimeo") {
      var vid = parseVimeoId(url);
      if (!vid) return "";
      return (
        '<iframe class=\"invento-video\" src=\"https://player.vimeo.com/video/' +
        vid +
        '?autoplay=1\" frameborder=\"0\" allow=\"autoplay; fullscreen\" allowfullscreen></iframe>'
      );
    }

    return (
      '<iframe class=\"invento-video\" src=\"' +
      url +
      '\" frameborder=\"0\" allow=\"autoplay; fullscreen\" allowfullscreen></iframe>'
    );
  }

  function openOverlay($trigger) {
    var $container = $trigger.closest(".invento-featured-video");
    var type = $container.data("video-type");
    var url = $container.data("video-url");
    var $overlay = $(".invento-video-overlay");

    if (!type || !url || !$overlay.length) return;

    var player = buildPlayer(type, url);
    if (!player) return;

    $overlay.find(".invento-video-player").html(player);
    $overlay.attr("aria-hidden", "false").addClass("is-open");
    $("body").addClass("invento-video-open");
  }

  function closeOverlay() {
    var $overlay = $(".invento-video-overlay");
    $overlay.find(".invento-video-player").empty();
    $overlay.attr("aria-hidden", "true").removeClass("is-open");
    $("body").removeClass("invento-video-open");
  }

  $(document).on("click", ".invento-video-play", function () {
    openOverlay($(this));
  });

  $(document).on("click", ".invento-video-close", function () {
    closeOverlay();
  });

  $(document).on("click", ".invento-video-overlay", function (e) {
    if ($(e.target).is(".invento-video-overlay")) {
      closeOverlay();
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      closeOverlay();
    }
  });

  function setMainMedia(type, url, poster, videoType) {
    var $display = $(".invento-media-display");
    if (!$display.length) return;

    if (type === "video") {
      var posterMarkup = poster
        ? '<img src=\"' + poster + '\" alt=\"\" />'
        : '<div class=\"invento-video-placeholder\"></div>';
      var html =
        '<div class=\"invento-featured-video\" data-video-type=\"' +
        videoType +
        '\" data-video-url=\"' +
        url +
        '\">' +
        '<div class=\"invento-video-poster\">' +
        posterMarkup +
        '<button type=\"button\" class=\"invento-video-play\" aria-label=\"Play video\"></button>' +
        "</div></div>";
      $display.html(html);
    } else {
      var img =
        '<div class=\"invento-featured-image\"><img src=\"' +
        url +
        '\" alt=\"\" /></div>';
      $display.html(img);
    }
  }

  function openImageLightbox(url) {
    var $lightbox = $(".invento-image-lightbox");
    if (!$lightbox.length) return;
    $lightbox.find("img").attr("src", url);
    $lightbox.attr("aria-hidden", "false").addClass("is-open");
    $("body").addClass("invento-video-open");
  }

  function closeImageLightbox() {
    var $lightbox = $(".invento-image-lightbox");
    $lightbox.find("img").attr("src", "");
    $lightbox.attr("aria-hidden", "true").removeClass("is-open");
    $("body").removeClass("invento-video-open");
  }

  $(document).on("click", ".invento-thumb", function () {
    $(".invento-thumb").removeClass("is-active");
    $(this).addClass("is-active");
    var type = $(this).data("media-type");
    var url = $(this).data("media-url");
    var poster = $(this).data("media-poster");
    var videoType = $(this).data("video-type");

    if (type === "video") {
      setMainMedia("video", url, poster, videoType || "youtube");
    } else if (type === "image") {
      setMainMedia("image", url);
    }
  });

  $(document).ready(function () {
    $(".invento-thumb").first().addClass("is-active");
  });

  $(document).on("click", ".invento-media-display img", function () {
    if ($(this).closest(".invento-featured-video").length) {
      return;
    }
    var src = $(this).attr("src");
    if (src) {
      openImageLightbox(src);
    }
  });

  $(document).on("click", ".invento-image-lightbox-close", function () {
    closeImageLightbox();
  });

  $(document).on("click", ".invento-image-lightbox", function (e) {
    if ($(e.target).is(".invento-image-lightbox")) {
      closeImageLightbox();
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      closeImageLightbox();
    }
  });

  $(document).on("click", ".invento-thumb-prev", function () {
    var $viewport = $(this)
      .closest(".invento-gallery")
      .find(".invento-thumb-viewport");
    $viewport.animate({ scrollLeft: $viewport.scrollLeft() - 180 }, 200);
  });

  $(document).on("click", ".invento-thumb-next", function () {
    var $viewport = $(this)
      .closest(".invento-gallery")
      .find(".invento-thumb-viewport");
    $viewport.animate({ scrollLeft: $viewport.scrollLeft() + 180 }, 200);
  });

  // Shortcode gallery slider
  $(document).on("click", ".invento-sc-gallery-thumb", function () {
    var $gallery = $(this).closest(".invento-sc-gallery");
    var $main = $gallery.find(".invento-sc-gallery-main");

    $gallery.find(".invento-sc-gallery-thumb").removeClass("is-active");
    $(this).addClass("is-active");

    if ($(this).data("media-type") === "video") {
      // Show video poster with play button in main area
      var videoType = $(this).data("video-type");
      var videoUrl = $(this).data("video-url");
      var poster = $(this).data("poster");
      var videoHtml =
        '<div class="invento-featured-video invento-sc-gallery-video" data-video-type="' +
        videoType +
        '" data-video-url="' +
        videoUrl +
        '">' +
        '<div class="invento-video-poster">' +
        '<img class="invento-sc-gallery-image" src="' +
        poster +
        '" alt="" />' +
        '<button type="button" class="invento-video-play" aria-label="Play video"></button>' +
        "</div></div>";
      var $prev = $main.find(".invento-sc-gallery-prev").detach();
      var $next = $main.find(".invento-sc-gallery-next").detach();
      $main.empty();
      if ($prev.length) $main.append($prev);
      $main.append(videoHtml);
      if ($next.length) $main.append($next);
    } else {
      // Show image in main area
      var fullUrl = $(this).data("full");
      var lightboxUrl = $(this).data("lightbox") || fullUrl;
      var $videoWrap = $main.find(".invento-sc-gallery-video");
      if ($videoWrap.length) {
        // Replace video with plain image
        $videoWrap.replaceWith(
          '<img class="invento-sc-gallery-image" src="' +
            fullUrl +
            '" data-lightbox="' +
            lightboxUrl +
            '" alt="" />'
        );
      } else {
        var $img = $main.find(".invento-sc-gallery-image");
        $img.attr("src", fullUrl).attr("data-lightbox", lightboxUrl);
      }
    }

    // Auto-scroll thumbnail strip to keep active visible
    var $strip = $gallery.find(".invento-sc-gallery-thumbs");
    if ($strip.length) {
      var strip = $strip[0];
      var thumb = this;
      var thumbLeft = thumb.offsetLeft - strip.offsetLeft;
      var thumbRight = thumbLeft + thumb.offsetWidth;
      var viewLeft = strip.scrollLeft;
      var viewRight = viewLeft + strip.clientWidth;

      if (thumbLeft < viewLeft) {
        strip.scrollLeft = thumbLeft;
      } else if (thumbRight > viewRight) {
        strip.scrollLeft = thumbRight - strip.clientWidth;
      }
    }
  });

  $(document).on("click", ".invento-sc-gallery-prev", function () {
    var $gallery = $(this).closest(".invento-sc-gallery");
    var $thumbs = $gallery.find(".invento-sc-gallery-thumb");
    var $active = $thumbs.filter(".is-active");
    var idx = $thumbs.index($active);
    var prev = idx > 0 ? idx - 1 : $thumbs.length - 1;
    $thumbs.eq(prev).trigger("click");
  });

  $(document).on("click", ".invento-sc-gallery-next", function () {
    var $gallery = $(this).closest(".invento-sc-gallery");
    var $thumbs = $gallery.find(".invento-sc-gallery-thumb");
    var $active = $thumbs.filter(".is-active");
    var idx = $thumbs.index($active);
    var next = idx < $thumbs.length - 1 ? idx + 1 : 0;
    $thumbs.eq(next).trigger("click");
  });

  // Shortcode gallery lightbox
  (function () {
    var $lightbox, $lbImg, $lbCounter, $lbContent;
    var lbImages = [];
    var lbIndex = 0;
    var lbSourceGallery = null;

    // Inject lightbox DOM once
    $(function () {
      var html =
        '<div class="invento-sc-lightbox" aria-hidden="true">' +
        '<div class="invento-sc-lightbox-backdrop"></div>' +
        '<div class="invento-sc-lightbox-content">' +
        '<button type="button" class="invento-sc-lightbox-close" aria-label="Close">&times;</button>' +
        '<button type="button" class="invento-sc-lightbox-nav invento-sc-lightbox-prev" aria-label="Previous">&lsaquo;</button>' +
        '<div class="invento-sc-lightbox-img-wrap">' +
        '<img class="invento-sc-lightbox-img" src="" alt="" />' +
        "</div>" +
        '<button type="button" class="invento-sc-lightbox-nav invento-sc-lightbox-next" aria-label="Next">&rsaquo;</button>' +
        '<div class="invento-sc-lightbox-counter"><span class="invento-sc-lightbox-current">1</span> / <span class="invento-sc-lightbox-total">1</span></div>' +
        "</div>" +
        "</div>";
      $("body").append(html);
      $lightbox = $(".invento-sc-lightbox");
      $lbImg = $lightbox.find(".invento-sc-lightbox-img");
      $lbCounter = $lightbox.find(".invento-sc-lightbox-counter");
      $lbContent = $lightbox.find(".invento-sc-lightbox-content");
    });

    function showLbImage() {
      $lbImg.attr("src", lbImages[lbIndex]);
      $lightbox.find(".invento-sc-lightbox-current").text(lbIndex + 1);
      $lbContent.removeClass("is-zoomed");
    }

    function openLightbox() {
      showLbImage();
      $lightbox.find(".invento-sc-lightbox-total").text(lbImages.length);
      // Hide nav if single image
      $lightbox
        .find(".invento-sc-lightbox-nav")
        .toggle(lbImages.length > 1);
      $lbCounter.toggle(lbImages.length > 1);
      $lightbox.addClass("is-open").attr("aria-hidden", "false");
      $("body").addClass("invento-sc-lightbox-open");
    }

    function closeLightbox() {
      $lightbox.removeClass("is-open").attr("aria-hidden", "true");
      $("body").removeClass("invento-sc-lightbox-open");
      $lbContent.removeClass("is-zoomed");
      $lbImg.attr("src", "");
      // Sync inline gallery to current image
      if (lbSourceGallery) {
        var $thumbs = lbSourceGallery.find(".invento-sc-gallery-thumb");
        if ($thumbs.length > 0 && $thumbs.eq(lbIndex).length) {
          $thumbs.eq(lbIndex).trigger("click");
        }
      }
    }

    // Open on main image click
    $(document).on("click", ".invento-sc-gallery-image", function () {
      var $gallery = $(this).closest(".invento-sc-gallery");
      lbSourceGallery = $gallery;
      var $thumbs = $gallery.find(".invento-sc-gallery-thumb");

      lbImages = [];
      if ($thumbs.length) {
        $thumbs.each(function () {
          var url =
            $(this).data("lightbox") || $(this).data("full") || "";
          if (url) lbImages.push(url);
        });
        var $active = $thumbs.filter(".is-active");
        lbIndex = $thumbs.index($active);
        if (lbIndex < 0) lbIndex = 0;
      } else {
        // Single image, no thumbs
        var url =
          $(this).data("lightbox") || $(this).attr("src") || "";
        if (url) lbImages.push(url);
        lbIndex = 0;
      }

      if (lbImages.length) openLightbox();
    });

    // Close triggers
    $(document).on("click", ".invento-sc-lightbox-close", function () {
      closeLightbox();
    });
    $(document).on("click", ".invento-sc-lightbox-backdrop", function () {
      closeLightbox();
    });

    // Navigation
    $(document).on("click", ".invento-sc-lightbox-prev", function () {
      lbIndex = lbIndex > 0 ? lbIndex - 1 : lbImages.length - 1;
      showLbImage();
    });
    $(document).on("click", ".invento-sc-lightbox-next", function () {
      lbIndex = lbIndex < lbImages.length - 1 ? lbIndex + 1 : 0;
      showLbImage();
    });

    // Zoom toggle
    $(document).on("click", ".invento-sc-lightbox-img", function () {
      $lbContent.toggleClass("is-zoomed");
    });

    // Keyboard: Escape, Left, Right
    $(document).on("keydown", function (e) {
      if (!$lightbox || !$lightbox.hasClass("is-open")) return;

      if (e.key === "Escape") {
        closeLightbox();
        e.stopImmediatePropagation(); // Prevent quote modal handler
      } else if (e.key === "ArrowLeft") {
        lbIndex = lbIndex > 0 ? lbIndex - 1 : lbImages.length - 1;
        showLbImage();
      } else if (e.key === "ArrowRight") {
        lbIndex = lbIndex < lbImages.length - 1 ? lbIndex + 1 : 0;
        showLbImage();
      }
    });
  })();

  // Quote modal
  function openQuoteModal() {
    var $overlay = $(".invento-quote-overlay");
    if (!$overlay.length) return;

    var fieldProduct = $overlay.data("field-product");
    var fieldQuantity = $overlay.data("field-quantity");

    var productId = $overlay.data("product-id");
    if (productId && fieldProduct) {
      var $select = $overlay.find(
        'select[data-name="' + fieldProduct + '"]',
      );
      if (
        $select.length &&
        $select.find('option[value="' + productId + '"]').length
      ) {
        $select.val(String(productId)).trigger("change");
      }
    }

    if (fieldQuantity) {
      var $qtyInput = $(".invento-qty-input").first();
      if ($qtyInput.length) {
        var qty = $qtyInput.val() || "1";
        var $qtyField = $overlay.find(
          'input[data-name="' + fieldQuantity + '"]',
        );
        if ($qtyField.length) {
          $qtyField.val(qty).trigger("change");
        }
      }
    }

    $overlay.attr("aria-hidden", "false").addClass("is-open");
    $("body").addClass("invento-quote-open");
  }

  function closeQuoteModal() {
    var $overlay = $(".invento-quote-overlay");
    $overlay.attr("aria-hidden", "true").removeClass("is-open");
    $("body").removeClass("invento-quote-open");
  }

  $(document).on("click", ".xsto_request_quote", function (e) {
    e.preventDefault();
    openQuoteModal();
  });

  $(document).on("click", ".invento-quote-close", function () {
    closeQuoteModal();
  });

  $(document).on("click", ".invento-quote-overlay", function (e) {
    if ($(e.target).is(".invento-quote-overlay")) {
      closeQuoteModal();
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $(".invento-quote-overlay.is-open").length) {
      closeQuoteModal();
    }
  });

  $(document).on("click", ".invento-quote-button", function () {
    var $card = $(this).closest(
      ".invento-quote-button-wrap, .invento-product-card",
    );
    var $qty = $card.find(".invento-qty-input").first();
    var qtyVal = $qty.length ? parseInt($qty.val(), 10) || 1 : 1;
    var productName = $(this).data("product-name") || "";

    try {
      var url = new URL($(this).attr("href"), window.location.origin);
      url.searchParams.set("product_name", productName);
      url.searchParams.set("product_qty", qtyVal);
      $(this).attr("href", url.toString());
    } catch (err) {
      // Ignore if URL parsing fails.
    }
  });

  // Category filter: shared AJAX loader
  function loadFilterProducts($wrap, page) {
    var $grid = $wrap.find(".invento-filter-grid");
    var $pagination = $wrap.find(".invento-filter-pagination");
    var activeCat =
      $wrap.find(".invento-filter-pill.is-active").data("category") || "";

    $grid.css("opacity", "0.5");

    $.post(InventoFront.ajax_url, {
      action: "invento_filter_products",
      nonce: InventoFront.nonce,
      category: activeCat,
      per_page: $wrap.data("per-page") || 12,
      layout: $wrap.data("layout") || "grid",
      paged: page,
    })
      .done(function (res) {
        if (res.success && res.data) {
          $grid.html(res.data.html);
          if ($pagination.length) {
            if (res.data.pagination) {
              $pagination.html(res.data.pagination).show();
            } else {
              $pagination.empty().hide();
            }
          } else if (res.data.pagination) {
            $grid.after(
              '<div class="invento-filter-pagination">' +
                res.data.pagination +
                "</div>"
            );
          }
        }
      })
      .always(function () {
        $grid.css("opacity", "1");
        // Scroll to top of filter section
        var el = document.getElementById("invento-catalog-filter");
        if (el) {
          el.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });

    // Update URL params
    var url = new URL(window.location);
    if (page > 1) {
      url.searchParams.set("product_page", page);
    } else {
      url.searchParams.delete("product_page");
    }
    window.history.replaceState({}, "", url);
  }

  // Category filter pills
  $(document).on("click", ".invento-filter-pill", function () {
    var $pill = $(this);
    var $wrap = $pill.closest(".invento-catalog-filter-wrap");

    $wrap.find(".invento-filter-pill").removeClass("is-active");
    $pill.addClass("is-active");

    // Update category URL param
    var cat = $pill.data("category") || "";
    var url = new URL(window.location);
    if (cat) {
      url.searchParams.set("product_category", cat);
    } else {
      url.searchParams.delete("product_category");
    }
    url.searchParams.delete("product_page");
    window.history.replaceState({}, "", url);

    loadFilterProducts($wrap, 1);
  });

  // Pagination click
  $(document).on("click", ".invento-filter-page", function () {
    if ($(this).prop("disabled") || $(this).hasClass("is-active")) return;
    var $wrap = $(this).closest(".invento-catalog-filter-wrap");
    var page = $(this).data("page");
    if (page) loadFilterProducts($wrap, page);
  });

  // Card thumb gallery shuffle on hover
  $(document).on("mouseenter", ".invento-card-thumb[data-gallery]", function () {
    var $thumb = $(this);
    if ($thumb.data("hovering")) return;

    var images = $thumb.data("gallery");
    // Handle both parsed array and raw string
    if (typeof images === "string") {
      try {
        images = JSON.parse(images);
      } catch (e) {
        return;
      }
    }
    if (!images || images.length < 2) return;

    var $img = $thumb.find("img").first();
    var originalSrc = $img.attr("src");
    var originalSrcset = $img.attr("srcset") || "";
    var idx = 0;

    $thumb.data("hovering", true);
    $thumb.data("original-src", originalSrc);
    $thumb.data("original-srcset", originalSrcset);

    // Remove srcset so browser uses src directly
    $img.removeAttr("srcset");

    var interval = setInterval(function () {
      idx = (idx + 1) % images.length;
      $img.animate({ opacity: 0 }, 200, function () {
        $img.attr("src", images[idx]);
        $img.animate({ opacity: 1 }, 200);
      });
    }, 1200);

    $thumb.data("interval", interval);
  });

  $(document).on("mouseleave", ".invento-card-thumb[data-gallery]", function () {
    var $thumb = $(this);
    clearInterval($thumb.data("interval"));
    $thumb.data("hovering", false);

    var $img = $thumb.find("img").first();
    var originalSrc = $thumb.data("original-src");
    var originalSrcset = $thumb.data("original-srcset");
    if (originalSrc) {
      $img.attr("src", originalSrc);
    }
    if (originalSrcset) {
      $img.attr("srcset", originalSrcset);
    }
  });

  $(".invento-qty-control").each(function () {
    const $wrapper = $(this);
    const $input = $wrapper.find(".counter");
    const min = parseInt($wrapper.data("min")) || 1;

    let value = parseInt($input.val()) || min;

    $wrapper.find(".invento-qty-plus").on("click", function () {
      value++;
      $input.val(value).trigger("change");
    });

    $wrapper.find(".invento-qty-minus").on("click", function () {
      if (value > min) {
        value--;
      } else {
        value = min;
      }
      $input.val(value).trigger("change");
    });
  });
  // Description "See more" toggle
  $(function () {
    $(".invento-sc-description").each(function () {
      var $wrap = $(this);
      var $content = $wrap.find(".invento-sc-description-content");
      var $toggle = $wrap.find(".invento-sc-description-toggle");

      // Content starts with max-height in CSS; check if it overflows
      var el = $content[0];
      if (el.scrollHeight > el.clientHeight + 1) {
        $wrap.addClass("is-collapsed");
        $toggle.show();
      } else {
        // Short content — remove the constraint
        $wrap.addClass("is-short");
      }
    });
  });

  $(document).on("click", ".invento-sc-description-toggle", function () {
    var $wrap = $(this).closest(".invento-sc-description");
    var isCollapsed = $wrap.hasClass("is-collapsed");

    if (isCollapsed) {
      $wrap.removeClass("is-collapsed").addClass("is-expanded");
      $(this).text("See less");
    } else {
      $wrap.removeClass("is-expanded").addClass("is-collapsed");
      $(this).text("See more");
    }
  });
})(jQuery);
