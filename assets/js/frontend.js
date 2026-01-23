(function ($) {
  function parseYouTubeId(url) {
    var match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
    return match ? match[1] : '';
  }

  function parseVimeoId(url) {
    var match = url.match(/vimeo\.com\/(?:video\/)?([0-9]+)/);
    return match ? match[1] : '';
  }

  function buildPlayer(type, url) {
    if (type === 'self_hosted') {
      return '<video class=\"invento-video\" controls playsinline src=\"' + url + '\"></video>';
    }

    if (type === 'youtube') {
      var id = parseYouTubeId(url);
      if (!id) return '';
      return '<iframe class=\"invento-video\" src=\"https://www.youtube.com/embed/' + id + '?autoplay=1\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>';
    }

    if (type === 'vimeo') {
      var vid = parseVimeoId(url);
      if (!vid) return '';
      return '<iframe class=\"invento-video\" src=\"https://player.vimeo.com/video/' + vid + '?autoplay=1\" frameborder=\"0\" allow=\"autoplay; fullscreen\" allowfullscreen></iframe>';
    }

    return '<iframe class=\"invento-video\" src=\"' + url + '\" frameborder=\"0\" allow=\"autoplay; fullscreen\" allowfullscreen></iframe>';
  }

  function openOverlay($trigger) {
    var $container = $trigger.closest('.invento-featured-video');
    var type = $container.data('video-type');
    var url = $container.data('video-url');
    var $overlay = $('.invento-video-overlay');

    if (!type || !url || !$overlay.length) return;

    var player = buildPlayer(type, url);
    if (!player) return;

    $overlay.find('.invento-video-player').html(player);
    $overlay.attr('aria-hidden', 'false').addClass('is-open');
    $('body').addClass('invento-video-open');
  }

  function closeOverlay() {
    var $overlay = $('.invento-video-overlay');
    $overlay.find('.invento-video-player').empty();
    $overlay.attr('aria-hidden', 'true').removeClass('is-open');
    $('body').removeClass('invento-video-open');
  }

  $(document).on('click', '.invento-video-play', function () {
    openOverlay($(this));
  });

  $(document).on('click', '.invento-video-close', function () {
    closeOverlay();
  });

  $(document).on('click', '.invento-video-overlay', function (e) {
    if ($(e.target).is('.invento-video-overlay')) {
      closeOverlay();
    }
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeOverlay();
    }
  });

  function setMainMedia(type, url, poster, videoType) {
    var $display = $('.invento-media-display');
    if (!$display.length) return;

    if (type === 'video') {
      var posterMarkup = poster
        ? '<img src=\"' + poster + '\" alt=\"\" />'
        : '<div class=\"invento-video-placeholder\"></div>';
      var html =
        '<div class=\"invento-featured-video\" data-video-type=\"' + videoType + '\" data-video-url=\"' + url + '\">' +
        '<div class=\"invento-video-poster\">' +
        posterMarkup +
        '<button type=\"button\" class=\"invento-video-play\" aria-label=\"Play video\"></button>' +
        '</div></div>';
      $display.html(html);
    } else {
      var img = '<div class=\"invento-featured-image\"><img src=\"' + url + '\" alt=\"\" /></div>';
      $display.html(img);
    }
  }

  function openImageLightbox(url) {
    var $lightbox = $('.invento-image-lightbox');
    if (!$lightbox.length) return;
    $lightbox.find('img').attr('src', url);
    $lightbox.attr('aria-hidden', 'false').addClass('is-open');
    $('body').addClass('invento-video-open');
  }

  function closeImageLightbox() {
    var $lightbox = $('.invento-image-lightbox');
    $lightbox.find('img').attr('src', '');
    $lightbox.attr('aria-hidden', 'true').removeClass('is-open');
    $('body').removeClass('invento-video-open');
  }

  $(document).on('click', '.invento-thumb', function () {
    $('.invento-thumb').removeClass('is-active');
    $(this).addClass('is-active');
    var type = $(this).data('media-type');
    var url = $(this).data('media-url');
    var poster = $(this).data('media-poster');
    var videoType = $(this).data('video-type');

    if (type === 'video') {
      setMainMedia('video', url, poster, videoType || 'youtube');
    } else if (type === 'image') {
      setMainMedia('image', url);
    }
  });

  $(document).ready(function () {
    $('.invento-thumb').first().addClass('is-active');
  });

  $(document).on('click', '.invento-media-display img', function () {
    if ($(this).closest('.invento-featured-video').length) {
      return;
    }
    var src = $(this).attr('src');
    if (src) {
      openImageLightbox(src);
    }
  });

  $(document).on('click', '.invento-image-lightbox-close', function () {
    closeImageLightbox();
  });

  $(document).on('click', '.invento-image-lightbox', function (e) {
    if ($(e.target).is('.invento-image-lightbox')) {
      closeImageLightbox();
    }
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeImageLightbox();
    }
  });

  $(document).on('click', '.invento-thumb-prev', function () {
    var $viewport = $(this).closest('.invento-gallery').find('.invento-thumb-viewport');
    $viewport.animate({ scrollLeft: $viewport.scrollLeft() - 180 }, 200);
  });

  $(document).on('click', '.invento-thumb-next', function () {
    var $viewport = $(this).closest('.invento-gallery').find('.invento-thumb-viewport');
    $viewport.animate({ scrollLeft: $viewport.scrollLeft() + 180 }, 200);
  });

  function normalizeQty(value) {
    var qty = parseInt(value, 10);
    if (isNaN(qty) || qty < 1) return 1;
    return qty;
  }

  $(document).on('click', '.invento-qty-plus', function () {
    var $input = $(this).closest('.invento-qty-control').find('.invento-qty-input');
    $input.val(normalizeQty($input.val()) + 1).trigger('change');
  });

  $(document).on('click', '.invento-qty-minus', function () {
    var $input = $(this).closest('.invento-qty-control').find('.invento-qty-input');
    var next = normalizeQty($input.val()) - 1;
    $input.val(next < 1 ? 1 : next).trigger('change');
  });

  $(document).on('change', '.invento-qty-input', function () {
    var qty = normalizeQty($(this).val());
    $(this).val(qty);
  });

  $(document).on('click', '.invento-quote-button', function () {
    var $card = $(this).closest('.invento-quote-button-wrap, .invento-product-card');
    var $qty = $card.find('.invento-qty-input').first();
    var qtyVal = $qty.length ? normalizeQty($qty.val()) : 1;
    var productName = $(this).data('product-name') || '';

    try {
      var url = new URL($(this).attr('href'), window.location.origin);
      url.searchParams.set('product_name', productName);
      url.searchParams.set('product_qty', qtyVal);
      $(this).attr('href', url.toString());
    } catch (err) {
      // Ignore if URL parsing fails.
    }
  });
})(jQuery);
