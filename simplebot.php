<?php


define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

 
function request($method, $param)
{
	
	//create a GET request
	$request = API_URL;
	$request .= $method . '?';
	
	foreach( $param as $key => $value )
	{
		$request .= $key . '=' . $value . '&';
	}
	return file_get_contents($request);
	
}

/**
 * upload input files
 * to upload input files you must specify your input_type by adding # in the beginning of parameter key
 */

function request_sendfile($method, $param)
{
	

	$request = API_URL;
	$request .= $method;
	//$request .= "?chat_id=@atest22";
	$post_fields = array();
	foreach( $param as $key => $value )
	{
		if( $key[0] == '#' )
		{
			$key = substr($key,1);
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime_type = finfo_file($finfo, realpath($value));
			$post_fields[$key] = new CURLFile(realpath($value), $mime_type, realpath($value));

		}
		else
		{
			$post_fields[$key] = $value;
		}
		
	}


	$ch = curl_init(); 


	curl_setopt($ch, CURLOPT_URL, $request); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($ch, CURLOPT_CAINFO, CD . "cacert.pem");
	
	$result=  curl_exec($ch);
	echo curl_error($ch);
	curl_close($ch);
	return $result;
}