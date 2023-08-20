(function( $ ) {
	'use strict';

	$(function() {
		$( document.body ).on( 'updated_cart_totals', function(){
			console.log('refresh2')
			console.log(ajax_object.ajaxurl)

			$.ajax({
				url: ajax_object.ajaxurl,
				type: 'POST',
				data:{
					action: 'klimapi_get_projects'
				},
				success: function(data) {
					data = JSON.parse(data);
					fillProjects(data);
				}
			});
		});
	});

	$(function() {
		$( '.klimapi-project' ).on( 'click', function(element){

			$.ajax({
				url: ajax_object.ajaxurl,
				type: 'POST',
				data:{
					action: 'klimapi_select_project',
					order: $(element.currentTarget).data('klimapi-order'),
				},
				success: function(data) {
					data = JSON.parse(data);
					fillProjects(data);

					$("[name='update_cart']").removeAttr('disabled').trigger("click");
				}
			});
		});
	});

	function fillProjects(data) {

		data.orders.map((order, key) => {

			$('.klimapi-project.klimapi-order-' + key + ' .klimapi-project-header-title').text(order.project.title)
			$('.klimapi-project.klimapi-order-' + key + ' .klimapi-project-header-co2-kg').text(order.kg_amount)
			$('.klimapi-project.klimapi-order-' + key + ' .klimapi-project-header-co2-price').text(order.price)
			$('.klimapi-project.klimapi-order-' + key + ' .klimapi-project-header').css('background-image', 'linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 70%, rgba(0,0,0,1) 100%), url(' + order.project.images[0] + ')');
			$('.klimapi-project.klimapi-order-' + key).data('klimapi-order', order.order_id);

			if (data.selected === order.order_id) {
				$('.klimapi-project.klimapi-order-' + key + ' .klimapi-project-header').addClass('klimapi-project-selected');
			} else {
				$('.klimapi-project.klimapi-order-' + key + ' .klimapi-project-header').removeClass('klimapi-project-selected');
			}
		})
	}

})( jQuery );
