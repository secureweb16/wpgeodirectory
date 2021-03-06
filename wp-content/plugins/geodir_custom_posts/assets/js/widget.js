jQuery(function($){
	$(document).on('change', '[data-argument="post_type"] select', function(e) {
		geodir_cp_widget_post_type_changed(this);
	});
});

function geodir_cp_widget_post_type_changed(el) {
	var $el, $block, $category, post_type;
	
	$el	= jQuery(el);
	$block = $el.closest('.sd-shortcode-settings').length ? $el.closest('.sd-shortcode-settings') : $el.closest('.widget-inside');
	if (! jQuery('form#gd_listings', $block).length && jQuery('[name="id_base"]', $block).val() != 'gd_listings' && ! jQuery('form#gd_linked_posts', $block).length && jQuery('[name="id_base"]', $block).val() != 'gd_linked_posts') {
		return;
	}
	$category = jQuery('[data-argument="category"]', $block).find('select');
	$sort_by = jQuery('[data-argument="sort_by"]', $block).find('select');
	post_type = $el.val();

	if (!post_type) {
        return;
    }

	var data = {
        action: 'geodir_widget_post_type_field_options',
        post_type: post_type,
    }
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function() {
			$category.css({
                opacity: 0.5
            });
			$sort_by.css({
                opacity: 0.5
            });
        },
        success: function(res, textStatus, xhr) {
            data = res && typeof res == 'object' && typeof res.data != 'undefined' ? res.data : '';

			if (data && typeof data == 'object') {
				if (typeof data.category != 'undefined' && typeof data.category.options != 'undefined') {
					$category.html(data.category.options).trigger('change');
				}
				if (typeof data.sort_by != 'undefined' && typeof data.sort_by.options != 'undefined') {
					$sort_by.html(data.sort_by.options).trigger('change');
				}
			}

			$category.css({
                opacity: 1
            });
			$sort_by.css({
                opacity: 1
            });

			jQuery('body').trigger('geodir_widget_post_type_field_options', data);
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log(errorThrown);
			$category.html('').trigger('change');
			$category.css({
                opacity: 1
            });
			$sort_by.css({
                opacity: 1
            });
        }
    });	
}