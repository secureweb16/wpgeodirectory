jQuery(function($){
	if (geodir_cp_params.aui) {
		/* AUI */
		setTimeout(function(){
			geodir_cp_search_post_init_aui($);
		}, 1000);
	} else {
		geodir_cp_search_post_init($);
	}
	$('.geodir-fill-data').on('click', function(e) {
		geodir_cp_fill_data(this);
	});
});

function geodir_cp_search_post_init($) {
	try {
        $(document.body).on('geodir-select-init', function() {
			// select2 autocomplete search
			$(':input.geodir-select-search-post').filter(':not(.enhanced)').each(function() {
				var source_cpt = $(this).data('source-cpt');
				var dest_cpt = $(this).data('dest-cpt');
				if ( ! source_cpt || ! dest_cpt ) {
					return true;
				}
				var select2_args = {
					allowClear: $(this).data('allow_clear') ? true : false,
					placeholder: $(this).data('placeholder'),
					minimumInputLength: $(this).data('min-input-length') ? $(this).data('min-input-length') : '2',
					escapeMarkup: function(m) {
						return m;
					},
					ajax: {
						url: geodir_params.ajax_url,
						type: 'POST',
						dataType: 'json',
						delay: 250,
						data: function(params) {
							var data = {
								term: params.term,
								action: 'geodir_cp_search_posts',
								source_cpt: source_cpt,
								dest_cpt: dest_cpt,
								security: $(this).data('nonce')
							};
							if ( $(this).data('exclude') ) {
								data.exclude = $(this).data('exclude');
							}
							if ( $(this).data('include') ) {
								data.include = $(this).data('include');
							}
							if ( $(this).data('limit') ) {
								data.limit = $(this).data('limit');
							}
							return data;
						},
						processResults: function(data) {
							var terms = [];
							if (data) {
								$.each(data, function(id, text) {
									terms.push({
										id: id,
										text: text
									});
								});
							}
							return {
								results: terms
							};
						},
						cache: true
					}
				};
				select2_args = $.extend(select2_args, geodirSelect2FormatString());
				var $select2 = $(this).select2(select2_args);
				$select2.addClass('enhanced');
				$select2.data('select2').$container.addClass('gd-select2-container');
				$select2.data('select2').$dropdown.addClass('gd-select2-container');

				if ($(this).data('sortable')) {
					var $select = $(this);
					var $list = $(this).next('.select2-container').find('ul.select2-selection__rendered');

					$list.sortable({
						placeholder: 'ui-state-highlight select2-selection__choice',
						forcePlaceholderSize: true,
						items: 'li:not(.select2-search__field)',
						tolerance: 'pointer',
						stop: function() {
							$($list.find('.select2-selection__choice').get().reverse()).each(function() {
								var id = $(this).data('data').id;
								var option = $select.find('option[value="' + id + '"]')[0];
								$select.prepend(option);
							});
						}
					});
					// Keep multiselects ordered alphabetically if they are not sortable.
				} else if ($(this).prop('multiple')) {
					$(this).on('change', function() {
						var $children = $(this).children();
						$children.sort(function(a, b) {
							var atext = a.text.toLowerCase();
							var btext = b.text.toLowerCase();

							if (atext > btext) {
								return 1;
							}
							if (atext < btext) {
								return -1;
							}
							return 0;
						});
						$(this).html($children);
					});
				}
			});
        }).trigger('geodir-select-init');
        $('html').on('click', function(event) {
            if (this === event.target) {
                $(':input.geodir-select-search-post').filter('.select2-hidden-accessible').select2('close');
            }
        });
    } catch (err) {
        window.console.log(err);
    }
}

function geodir_cp_search_post_init_aui($) {
	try {
		// select2 autocomplete search
		$(':input.geodir-select-search-post').filter(':not(.geodir-enhanced)').each(function() {
			if ($(this).hasClass('select2-hidden-accessible')) {
				$(this).select2('destroy');
			}

			var source_cpt = $(this).data('source-cpt');
			var dest_cpt = $(this).data('dest-cpt');
			if ( ! source_cpt || ! dest_cpt ) {
				return true;
			}
			var select2_args = {
				allowClear: $(this).data('allow_clear') ? true : false,
				placeholder: $(this).data('placeholder'),
				minimumInputLength: $(this).data('min-input-length') ? $(this).data('min-input-length') : '2',
				escapeMarkup: function(m) {
					return m;
				},
				ajax: {
					url: geodir_params.ajax_url,
					type: 'POST',
					dataType: 'json',
					delay: 250,
					data: function(params) {
						var data = {
							term: params.term,
							action: 'geodir_cp_search_posts',
							source_cpt: source_cpt,
							dest_cpt: dest_cpt,
							security: $(this).data('nonce')
						};
						if ( $(this).data('exclude') ) {
							data.exclude = $(this).data('exclude');
						}
						if ( $(this).data('include') ) {
							data.include = $(this).data('include');
						}
						if ( $(this).data('limit') ) {
							data.limit = $(this).data('limit');
						}
						return data;
					},
					processResults: function(data) {
						var terms = [];
						if (data) {
							$.each(data, function(id, text) {
								terms.push({
									id: id,
									text: text
								});
							});
						}
						return {
							results: terms
						};
					},
					cache: true
				}
			};
			select2_args = $.extend(select2_args, geodirCPSelect2FormatString());
			var $select2 = $(this).select2(select2_args);
			$select2.addClass('enhanced geodir-enhanced').removeClass('aui-select2');

			if ($(this).data('sortable')) {
				var $select = $(this);
				var $list = $(this).next('.select2-container').find('ul.select2-selection__rendered');

				$list.sortable({
					placeholder: 'ui-state-highlight select2-selection__choice',
					forcePlaceholderSize: true,
					items: 'li:not(.select2-search__field)',
					tolerance: 'pointer',
					stop: function() {
						$($list.find('.select2-selection__choice').get().reverse()).each(function() {
							var id = $(this).data('data').id;
							var option = $select.find('option[value="' + id + '"]')[0];
							$select.prepend(option);
						});
					}
				});
				// Keep multiselects ordered alphabetically if they are not sortable.
			} else if ($(this).prop('multiple')) {
				$(this).on('change', function() {
					var $children = $(this).children();
					$children.sort(function(a, b) {
						var atext = a.text.toLowerCase();
						var btext = b.text.toLowerCase();

						if (atext > btext) {
							return 1;
						}
						if (atext < btext) {
							return -1;
						}
						return 0;
					});
					$(this).html($children);
				});
			}
		});
        $('html').on('click', function(event) {
            if (this === event.target) {
                $(':input.geodir-select-search-post').filter('.select2-hidden-accessible').select2('close');
            }
        });
    } catch (err) {
        window.console.log(err);
    }
}

function geodirCPSelect2FormatString() {
    return {
        'language': {
            errorLoading: function() {
                // Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
                return geodir_params.i18n_searching;
            },
            inputTooLong: function(args) {
                var overChars = args.input.length - args.maximum;
                if (1 === overChars) {
                    return geodir_params.i18n_input_too_long_1;
                }
                return geodir_params.i18n_input_too_long_n.replace('%item%', overChars);
            },
            inputTooShort: function(args) {
                var remainingChars = args.minimum - args.input.length;
                if (1 === remainingChars) {
                    return geodir_params.i18n_input_too_short_1;
                }
                return geodir_params.i18n_input_too_short_n.replace('%item%', remainingChars);
            },
            loadingMore: function() {
                return geodir_params.i18n_load_more;
            },
            maximumSelected: function(args) {
                if (args.maximum === 1) {
                    return geodir_params.i18n_selection_too_long_1;
                }
                return geodir_params.i18n_selection_too_long_n.replace('%item%', args.maximum);
            },
            noResults: function() {
                return geodir_params.i18n_no_matches;
            },
            searching: function() {
                return geodir_params.i18n_searching;
            }
        }
    };
}

function geodir_cp_fill_data(el) {
	var $el, $form, $row, $field, _confirm, from_post_type, to_post_type, from_post_id, to_post_id;

	$el = jQuery(el);
	$fa = jQuery('.fa-sync-alt', $el);
	if ($fa.hasClass('fa-spin')) {
		return;
	}
	$form = $el.closest('form');
	$row = geodir_cp_params.aui ? $el.closest('.form-group') : $el.closest('.geodir_form_row');
	from_post_type = $el.data( 'from-post-type' );
	to_post_type = $el.data( 'to-post-type' );
	if (!from_post_type || !to_post_type) {
		return;
	}
	from_post_id = $el.data( 'from-post-id' );
	$field = jQuery('select[name^="' + to_post_type + '"]', $row);
	to_post_id = $field.val();
	if (!to_post_id) {
		$field.focus();
		return;
	}
	if (typeof to_post_id == 'object') {
		to_post_id = to_post_id[0];
	}
	_confirm = $el.data('confirm');
	if (_confirm && !confirm(_confirm)) {
		return;
	}
	$el.addClass('disabled');
	$fa.addClass('fa-spin');

	var data = {
		action: 'geodir_cp_fill_data',
		from_post_type: from_post_type,
		to_post_type: to_post_type,
		from_post_id: from_post_id,
		to_post_id: to_post_id,
		security: geodir_params.basic_nonce
	}
	jQuery.ajax({
		url: geodir_params.ajax_url,
		type: 'POST',
		dataType: 'json',
		data: data,
		beforeSend: function() {
		},
		success: function(res, textStatus, xhr) {
			_data = res && typeof res == 'object' && typeof res.data != 'undefined' ? res.data : '';

			if (_data) {
				if (typeof _data == 'object') {
					jQuery.each(_data, function(field, value) {
						if (jQuery('[name="' + field + '"]', $form).length) {
							jQuery('[name="' + field + '"]', $form).val(value);
						}
					});
					if (_data.latitude && _data.longitude && jQuery('[name="latitude"]', $form).length && jQuery('[name="longitude"]', $form).length) {
						// Update map marker
						user_address = true;// so the marker move does not change the address
						if (window.gdMaps == 'google') {
							latlon = new google.maps.LatLng(_data.latitude, _data.longitude);
							jQuery.goMap.map.setCenter(latlon);
							updateMarkerPosition(latlon);
							centerMarker();
							google.maps.event.trigger(baseMarker, 'dragend');
						} else if (window.gdMaps == 'osm') {
							latlon = new L.latLng(_data.latitude, _data.longitude);
							jQuery.goMap.map.setView(latlon, jQuery.goMap.map.getZoom());
							updateMarkerPositionOSM(latlon);
							centerMarker();
							baseMarker.fireEvent('dragend');
						}

						setTimeout(function () {
							if (window.gdMaps == 'google') {
								google.maps.event.trigger(baseMarker, 'dragend');
							} else if (window.gdMaps == 'osm') {
								baseMarker.fireEvent('dragend');
							}
						}, 1600);
					}
					jQuery('body').trigger('geodir_cp_fill_data', res);
				} else {
					console.log(_data);
				}
			}
			$el.removeClass('disabled');
			$fa.removeClass('fa-spin');
		},
		error: function(xhr, textStatus, errorThrown) {
			console.log(errorThrown);
			$el.removeClass('disabled');
			$fa.removeClass('fa-spin');
		}
	});
}