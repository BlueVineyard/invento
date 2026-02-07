(function ($) {
  function safeParse(value) {
    if (!value) return [];
    try {
      var parsed = JSON.parse(value);
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  function renderRepeater($wrapper) {
    var type = $wrapper.data('repeater');
    var $input = $wrapper.find('.invento-repeater-input');
    var rows = safeParse($input.val());
    var templateId = '';

    if (type === 'variations') templateId = '#invento-variation-template';
    if (type === 'icon_text') templateId = '#invento-icon-text-template';
    if (type === 'main_features') templateId = '#invento-feature-template';
    if (type === 'specifications') templateId = '#invento-specification-template';

    var template = $(templateId).html() || '';
    var $rowsContainer = $wrapper.find('.invento-repeater-rows');
    $rowsContainer.empty();

    rows.forEach(function (row) {
      var $row = $(template);
      $row.find('[data-field="name"]').val(row.name || '');
      $row.find('[data-field="options"]').val(Array.isArray(row.options) ? row.options.join(', ') : (row.options || ''));
      $row.find('[data-field="label"]').val(row.label || '');
      $row.find('[data-field="value"]').val(row.value || '');
      var iconVal = row.icon_type || '';
      $row.find('[data-field="icon_type"]').val(iconVal);
      if (iconVal && !isNaN(iconVal) && parseInt(iconVal) > 0) {
        var $upload = $row.find('.invento-icon-upload');
        var attachmentId = parseInt(iconVal);
        var att = wp.media.attachment(attachmentId);
        (function($upload, att) {
          att.fetch().then(function() {
            var url = att.get('url');
            if (url) {
              $upload.find('.invento-icon-preview').attr('src', url).show();
              $upload.find('.invento-icon-remove').show();
            }
          });
        })($upload, att);
      }
      $row.find('[data-field="title"]').val(row.title || '');
      $row.find('[data-field="description"]').val(row.description || '');
      $rowsContainer.append($row);
    });
  }

  function serializeRepeater($wrapper) {
    var type = $wrapper.data('repeater');
    var rows = [];

    $wrapper.find('.invento-repeater-row').each(function () {
      var $row = $(this);
      var data = {};

      if (type === 'variations') {
        data.name = $row.find('[data-field="name"]').val() || '';
        var optionsRaw = $row.find('[data-field="options"]').val() || '';
        data.options = optionsRaw
          .split(',')
          .map(function (item) {
            return item.trim();
          })
          .filter(function (item) {
            return item.length > 0;
          });
      }

      if (type === 'icon_text') {
        data.icon_type = $row.find('[data-field="icon_type"]').val() || '';
        data.title = $row.find('[data-field="title"]').val() || '';
        data.description = $row.find('[data-field="description"]').val() || '';
      }

      if (type === 'main_features') {
        data.title = $row.find('[data-field="title"]').val() || '';
        data.description = $row.find('[data-field="description"]').val() || '';
      }

      if (type === 'specifications') {
        data.label = $row.find('[data-field="label"]').val() || '';
        data.value = $row.find('[data-field="value"]').val() || '';
      }

      rows.push(data);
    });

    $wrapper.find('.invento-repeater-input').val(JSON.stringify(rows));
  }

  function bindRepeater($wrapper) {
    renderRepeater($wrapper);

    $wrapper.on('click', '.invento-repeater-add', function () {
      var type = $wrapper.data('repeater');
      var templateId = '';

      if (type === 'variations') templateId = '#invento-variation-template';
      if (type === 'icon_text') templateId = '#invento-icon-text-template';
      if (type === 'main_features') templateId = '#invento-feature-template';
      if (type === 'specifications') templateId = '#invento-specification-template';

      var template = $(templateId).html() || '';
      $wrapper.find('.invento-repeater-rows').append($(template));
      serializeRepeater($wrapper);
    });

    $wrapper.on('click', '.invento-repeater-remove', function () {
      $(this).closest('.invento-repeater-row').remove();
      serializeRepeater($wrapper);
    });

    $wrapper.on('input change', 'input, textarea, select', function () {
      serializeRepeater($wrapper);
    });

    $wrapper.closest('form').on('submit', function () {
      serializeRepeater($wrapper);
    });

    $wrapper.on('click', '.invento-icon-select', function (e) {
      e.preventDefault();
      var $upload = $(this).closest('.invento-icon-upload');
      var frame = wp.media({
        title: 'Select Icon Image',
        multiple: false,
        library: { type: 'image' }
      });

      frame.on('select', function () {
        var attachment = frame.state().get('selection').first();
        if (!attachment) return;
        var id = attachment.get('id');
        var url = attachment.get('url');
        $upload.find('[data-field="icon_type"]').val(id);
        $upload.find('.invento-icon-preview').attr('src', url).show();
        $upload.find('.invento-icon-remove').show();
        serializeRepeater($wrapper);
      });

      frame.open();
    });

    $wrapper.on('click', '.invento-icon-remove', function (e) {
      e.preventDefault();
      var $upload = $(this).closest('.invento-icon-upload');
      $upload.find('[data-field="icon_type"]').val('');
      $upload.find('.invento-icon-preview').attr('src', '').hide();
      $(this).hide();
      serializeRepeater($wrapper);
    });
  }

  function renderGallery(ids) {
    var $preview = $('.invento-gallery-preview');
    $preview.empty();

    if (!ids.length) return;

    ids.forEach(function (id) {
      var attachment = wp.media.attachment(id);
      attachment.fetch().then(function () {
        var url = attachment.get('sizes') && attachment.get('sizes').thumbnail
          ? attachment.get('sizes').thumbnail.url
          : attachment.get('url');
        if (url) {
          $preview.append('<img src="' + url + '" alt="" data-id="' + id + '" />');
        }
      });
    });
  }

  function bindGallery() {
    var $input = $('#invento_gallery_ids');
    if (!$input.length) return;

    var ids = safeParse($input.val());
    renderGallery(ids);

    $('.invento-gallery-select').on('click', function (e) {
      e.preventDefault();
      var frame = wp.media({
        title: InventoAdmin.strings.selectImgs,
        multiple: true,
        library: { type: 'image' }
      });

      frame.on('select', function () {
        var selection = frame.state().get('selection');
        var ids = [];
        selection.each(function (attachment) {
          ids.push(attachment.get('id'));
        });
        $input.val(JSON.stringify(ids));
        renderGallery(ids);
      });

      frame.open();
    });
  }

  function toggleStockFields() {
    var mode = $('#invento_stock_mode').val();
    $('.invento-stock-field').hide();
    if (mode === 'simple') {
      $('.invento-stock-quantity').show();
    }
    if (mode === 'label') {
      $('.invento-stock-label').show();
    }
  }

  function bindVideoSelect() {
    $('.invento-video-select').on('click', function (e) {
      e.preventDefault();
      var target = $(this).data('target');
      var $input = $(target);
      if (!$input.length) return;

      var frame = wp.media({
        title: 'Select Video',
        multiple: false,
        library: { type: 'video' }
      });

      frame.on('select', function () {
        var selection = frame.state().get('selection').first();
        if (!selection) return;
        var url = selection.get('url');
        if (url) {
          $input.val(url).trigger('change');
          $('#invento_featured_video_type').val('self_hosted');
        }
      });

      frame.open();
    });
  }

  $(document).ready(function () {
    $('.invento-repeater').each(function () {
      bindRepeater($(this));
    });

    bindGallery();
    bindVideoSelect();

    toggleStockFields();
    $('#invento_stock_mode').on('change', toggleStockFields);

    function bindOrderList($list, $input) {
      if (!$list.length || !$input.length) return;

      $list.sortable({
        handle: '.invento-drag-handle',
        axis: 'y'
      });

      function syncOrder() {
        var items = [];
        $list.find('.invento-template-order-item').each(function () {
          var $item = $(this);
          items.push({
            key: $item.data('key'),
            enabled: $item.find('input[type=\"checkbox\"]').is(':checked')
          });
        });
        $input.val(JSON.stringify(items));
      }

      $list.on('change', 'input[type=\"checkbox\"]', syncOrder);
      $list.on('sortupdate', syncOrder);
      $list.on('sortstop', syncOrder);
      syncOrder();
    }

    bindOrderList($('.invento-template-order-list').first(), $('.invento-template-order-input').first());
    bindOrderList($('.invento-grid-order-list'), $('.invento-grid-order-input'));

  });

  // Term image uploader
  $(document).on('click', '.invento-term-image-upload', function (e) {
    e.preventDefault();
    var $wrap = $(this).closest('.invento-term-image-wrap');
    var frame = wp.media({ multiple: false, library: { type: 'image' } });
    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      $wrap.find('.invento-term-image-id').val(attachment.id);
      $wrap.find('.invento-term-image-preview').html('<img src="' + (attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url) + '" alt="" />');
      $wrap.find('.invento-term-image-remove').show();
    });
    frame.open();
  });

  $(document).on('click', '.invento-term-image-remove', function (e) {
    e.preventDefault();
    var $wrap = $(this).closest('.invento-term-image-wrap');
    $wrap.find('.invento-term-image-id').val('');
    $wrap.find('.invento-term-image-preview').empty();
    $(this).hide();
  });
})(jQuery);
