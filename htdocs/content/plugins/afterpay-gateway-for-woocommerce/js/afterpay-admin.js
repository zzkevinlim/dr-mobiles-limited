jQuery(function($) {
	$('select#woocommerce_afterpay_testmode')
		.on('change', function(event) {
			if ($(this).val() != 'production') {
				$('input#woocommerce_afterpay_prod-id').closest('tr').hide();
				$('input#woocommerce_afterpay_prod-secret-key').closest('tr').hide();
				$('input#woocommerce_afterpay_test-id').closest('tr').show();
				$('input#woocommerce_afterpay_test-secret-key').closest('tr').show();
			} else {
				$('input#woocommerce_afterpay_prod-id').closest('tr').show();
				$('input#woocommerce_afterpay_prod-secret-key').closest('tr').show();
				$('input#woocommerce_afterpay_test-id').closest('tr').hide();
				$('input#woocommerce_afterpay_test-secret-key').closest('tr').hide();
			}
		})
		.trigger('change');

	$('a#reset-to-default-link').on('click',function(event){
		$.ajax(
		{
			type: "post",
			url: afterpay_ajax_object.ajax_url,
			data: {'action': 'afterpay_action'},
			success: function(response){
				$.each(response,function(index, element){
					var $el = $("#woocommerce_afterpay_"+index);
					var attr_type= $el.attr("type");

					if(attr_type == 'text'  || attr_type=='textarea' || attr_type=='number' || $el.is('select')){
						$el.val(element);
					}
					else if(attr_type == "checkbox"){
						$el.prop('checked', element == 'yes');
					}
					else{
						tinymce.get(index.replace(/-/g, "")).setContent(element);
					}
				});
				$('textarea[id$="placement-attributes"]').trigger('keyup');
				alert('Customisations have now been reset to defaults. Please review and click "Save Changes" to accept the new values.');
			}
		});
	});

	$('a.afterpay-notice-dismiss').on('click',function(event){
		var review_link;
		if(this.hasAttribute('href')){
			review_link = $(this).attr("href");
			event.preventDefault();
		}
		var noticeClass = $(this).attr("class");
		$.ajax(
		{
			type: "post",
			url: afterpay_ajax_object.ajax_url,
			data: {'action': 'afterpay_notice_dismiss_action'},
			success: function(response){
				if(response){
					if(noticeClass.includes("afterpay_rate_redirect")){
						$(".afterpay-rating-notice").hide();
						window.open(review_link,"_blank");
					}
					else{
						location.reload();
					}
				}
			}
		});
	});

	const initPlacement = function(field_name){
		const $target = $('textarea#woocommerce_afterpay_' + field_name);
		const regex = /data(-[a-z]+)+="[^"]+"/g;

		$target.on('keyup', function(event){
			let attributes = {};
			const rawArray = $(this).val().trim().match(regex);
			$.each(rawArray, function(i, raw){
				const keys = raw.match(/data(-[a-z]+)+(?==")/);
				const values = raw.match(/(?<==")[^"]+(?=")/);
				if (keys && values) {
					attributes[keys[0]] = values[0];
				}
			});
			attributes['data-currency'] = afterpay_config.currency;
			attributes['data-locale'] = afterpay_config.locale;
			attributes['data-amount'] = afterpay_config.max;

			$target.nextAll('afterpay-placement').remove();

			$('<afterpay-placement>').attr(attributes).appendTo($target.parent());
		}).trigger('keyup');
	};

	const script = document.createElement('script');
	script.src = "https://js.afterpay.com/afterpay-1.x.js";
	script.dataset.min = afterpay_config.min;
	script.dataset.max = afterpay_config.max;
	script.onload = function () {
		initPlacement('category-pages-placement-attributes');
		initPlacement('product-pages-placement-attributes');
		initPlacement('product-variant-placement-attributes');
		initPlacement('cart-page-placement-attributes');
	};
	document.head.appendChild(script);
});
