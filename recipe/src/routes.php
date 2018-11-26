<?php
    use Slim\Http\Request;
    use Slim\Http\Response;
	use Slim\Http\UploadedFile;
    // Routes
	
	$container = $app->getContainer();
	$container['upload_directory'] = $_SERVER['DOCUMENT_ROOT']. '/u';
	
    $app->get("/", function(Request $request, Response $response, array $args) {
        $api  = new ShopifyClient('dad9af588e8f942d5acb1cdacc98d503', '', 'org100h.myshopify.com/admin/', '90e9b67380e181d139d6dd1a511f2be2');
        $data = $api->call('orders.json', 'GET');
		$orders = json_decode($data)->orders;
		$response = $this->view->render($response, 'index.phtml', ['orders' => $orders ]);						
        return $response;
    });
	
	$app->post("/upload", function(Request $request, Response $response, array $args) {
		
		$directory = $this->get('upload_directory');
		
		$uploadedFiles = $request->getUploadedFiles();
		$uploadedFile = $uploadedFiles['image'];
		$media = $uploadedFile->getClientMediaType();
		
		if(!in_array($media, array('image/jpeg','application/pdf'))){
			return $response->withJson(array('asset' => array( 'error' => 'Only PDF or JPG allowed' )) );
			exit();
		}
							
		$extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
		$basename = bin2hex(random_bytes(8)); 
		$filename = sprintf('%s.%0.8s', $basename, $extension);				
		$uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
				
		$api  = new ShopifyClient('dad9af588e8f942d5acb1cdacc98d503', 'c58405e60826f904b1c251c796ba7a15', 'org100h.myshopify.com/admin', '90e9b67380e181d139d6dd1a511f2be2');
       
		$theme_id = $api->call('themes.json', 'GET');
		$theme_id = json_decode($theme_id)->themes[0]->id;
		
		$put_asset = $api->call('/themes/'.$theme_id.'/assets.json', 'PUT', array(  
			"asset" => array(
				"key" => 'assets/'.$filename,
				"src" => 'https://redpanda.ml/u/'.$filename
			))
		);		
		$asset = json_decode( $put_asset );
		if( $asset->asset->key ){
			unlink( $directory . DIRECTORY_SEPARATOR . $filename );
		}	
		
		return $response->withJson(json_decode($put_asset));			
	});
	
	$app->post("/delete", function(Request $request, Response $response) {
		
		$data = $request->getParsedBody();
		$api  = new ShopifyClient('dad9af588e8f942d5acb1cdacc98d503', 'c58405e60826f904b1c251c796ba7a15', 'org100h.myshopify.com/admin', '90e9b67380e181d139d6dd1a511f2be2');
		
		$theme_id = $api->call('themes.json', 'GET');
		$theme_id = json_decode($theme_id)->themes[0]->id;
		
		$key = filter_var($data['key'], FILTER_SANITIZE_STRING);		
		$order = filter_var($data['order'], FILTER_SANITIZE_STRING);		
		$name = filter_var($data['name'], FILTER_SANITIZE_STRING);		
		
		$key = explode('?',$key)[0];
		
		$asset_deleted = $api->call('themes/'.$theme_id.'/assets.json?asset[key]=assets/'.$key, 'DELETE');		
		//$order_updated = $api->call('/orders/'.$order.'.json', 'PUT', array('order' => array( 'id' => $order, 'note_attributes' => null)));		
		
		$response->getBody()->write(var_export($asset_deleted , true));
		return $response;
		//return $response->withJson(json_decode($order_updated));
	
			
	});
	
	
	
	
