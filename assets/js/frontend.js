(function ($) {
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
    $gallery.find(".invento-sc-gallery-thumb").removeClass("is-active");
    $(this).addClass("is-active");
    $gallery
      .find(".invento-sc-gallery-image")
      .attr("src", $(this).data("full"));

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
})(jQuery);
