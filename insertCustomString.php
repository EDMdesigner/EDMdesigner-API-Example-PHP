<?php

	$publicId = "API_EDM";//"TESTAPIKEY";
	$magic = "3dsd345Gasaz9u3s234d";//"XSDE422RSDJQDJW8QADM31SMA";
	$user = 'admin';

	$getTokenUrl = "http://127.0.0.1:3001/api/token";
	$addCustomStringsUrl = "http://127.0.0.1:3001/json/user/addCustomStrings";
	$addCustomStringsToUserUrl = "http://127.0.0.1:3001/json/user/addCustomStringsToUser";

	// handshake
	$ip = $_SERVER["REMOTE_ADDR"];
	$timestamp = time();
	$hash = md5($publicId . $ip . $timestamp . $magic);
	$data = array(
			"id"	=> $publicId,
			"uid"	=> $user,
			"ip"	=> $ip,
			"ts"	=> $timestamp,
			"hash"	=> $hash
	);
	$tokenResult = json_decode(sendPostRequest($getTokenUrl, $data), TRUE);
	$token = ($tokenResult['token']) ? $tokenResult['token'] : FALSE;
	$tokenValidation = 'Token validated: '.$token;
	
	print $tokenValidation;
	print '<br>';

	// addCustomString to ApiPartner
	$customStrings = array();
	$customStrings['items'] = array();
	$customStrings['items'][] = array('label' => 'testLabel1', 'replacer' => 'testReplacer1');
	$customStrings['items'][] = array('label' => 'testLabel2', 'replacer' => 'testReplacer2');
	$customStrings['items'][] = array('label' => 'testLabel3', 'replacer' => 'testReplacer3');

	$addCustomStringsUrl.= '?token='.$token.'&user='.$user;
	$addCustomStringResult = 'AddCustomString result: '.sendPostRequest($addCustomStringsUrl, $customStrings);
	print($addCustomStringResult);
	print '<br>';

	// addCustomString to ApiUser
	$customStrings = array();
	$customStrings['userId'] = '52d79ebda174c400000020b7'; // It must be one of your existing user's id, otherwise returns error
	$customStrings['items'] = array();
	$customStrings['items'][] = array('label' => 'testLabel1', 'replacer' => 'testReplacer1');
	$customStrings['items'][] = array('label' => 'testLabel2', 'replacer' => 'testReplacer2');
	$customStrings['items'][] = array('label' => 'testLabel3', 'replacer' => 'testReplacer3');

	$addCustomStringsToUserUrl.= '?token='.$token.'&user='.$user;
	$addCustomStringsToUserResult = 'addCustomStringsToUser result: '.sendPostRequest($addCustomStringsToUserUrl, $customStrings);
	print $addCustomStringsToUserResult;

	/// utils
	function sendPostRequest($url, $data) {

		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    )
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$response = parse_http_response_header($http_response_header);

		$statusCode = $response[0]['status']['code'];

		if($statusCode === 200) {
			return $result;
		} else {
			die(print($statusCode));			
		}
	}

	function parse_http_response_header(array $headers)
	{
	    $responses = array();
	    $buffer = NULL;
	    foreach ($headers as $header)
	    {
	        if ('HTTP/' === substr($header, 0, 5))
	        {
	            // add buffer on top of all responses
	            if ($buffer) array_unshift($responses, $buffer);
	            $buffer = array();

	            list($version, $code, $phrase) = explode(' ', $header, 3) + array('', FALSE, '');

	            $buffer['status'] = array(
	                'line' => $header,
	                'version' => $version,
	                'code' => (int) $code,
	                'phrase' => $phrase
	            );
	            $fields = &$buffer['fields'];
	            $fields = array();
	            continue;
	        }
	        list($name, $value) = explode(': ', $header, 2) + array('', '');
	        // header-names are case insensitive
	        $name = strtoupper($name);
	        // values of multiple fields with the same name are normalized into
	        // a comma separated list (HTTP/1.0+1.1)
	        if (isset($fields[$name]))
	        {
	            $value = $fields[$name].','.$value;
	        }
	        $fields[$name] = $value;
	    }
	    unset($fields); // remove reference
	    array_unshift($responses, $buffer);

	    return $responses;
	}
?>