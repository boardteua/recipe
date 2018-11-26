jQuery( document ).ready(function($) {

	$('.pagination li a').click(function(e){
			e.preventDefault();

			$.ajax({
				url: conf.plugin_url + 'index.php',
				method: 'POST',
				data: {page: $(this).data('page')},
				dataType: 'json'
			}).done(function(data, textStatus, jqXHR) {
				// because dataType is json 'data' is guaranteed to be an object
				console.log('done');
			}).fail(function(jqXHR, textStatus, errorThrown) {
				// the response is not guaranteed to be json
				if (jqXHR.responseJSON) {
					// jqXHR.reseponseJSON is an object
					console.log('failed with json data');
				}
				else {
					// jqXHR.responseText is not JSON data
					console.log('failed with unknown data'); 
				}
			}).always(function(dataOrjqXHR, textStatus, jqXHRorErrorThrown) {
				console.log('always');
			});
			
	});
});