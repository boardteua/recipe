jQuery( document ).ready(function($) {
 $(".asset_upload").on("change", 'input[type="file"]', function (e) {
	
	 var $files = $(this).get(0).files;
	 if ($files.length) {
		
		var settings = {
                async: true,
                crossDomain: true,
                processData: false,
                contentType: false,
                type: "POST",
				dataType: "jsonp",
                url: 'https://org100h.myshopify.com/apps/recipe/upload'
            };
		
		
		var formData = new FormData();
        formData.append("image", $files[0]);
        settings.data = formData;
		
		   $.ajax(settings).done(function (response) {
                img = JSON.parse(response);

					console.log(img);
			   
                
            });
		
	 }
	
 });
});