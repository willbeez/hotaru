<?php

/**
* gets the meta data from a website
*/
function fetchMeta($url = '')
{
   if (!$url) return false;

   // check whether we have http at the zero position of the string
   if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) $url = 'http://' . $url;          

   $fp = @fopen( $url, 'r' );

   if (!$fp) return false;

   $content = '';

   while( !feof( $fp ) ) {
       $buffer = trim( fgets( $fp, 4096 ) );
       $content .= $buffer;
   }

   $start = '<title>';
   $end = '<\/title>';

   preg_match( "/$start(.*)$end/s", $content, $match );
   $title = isset($match) ? $match[1] : ''; 

   $metatagarray = get_meta_tags( $url );

   $keywords = isset($metatagarray[ "keywords" ]) ? $metatagarray[ "keywords" ] : '';
   $description = isset($metatagarray[ "description" ]) ? $metatagarray[ "description" ] : '';

   return array('title' => $title, 'keywords' => $keywords, 'description' => $description);
}
    
    
function hotaru_http_request($url)
{
        $response = "";
        
	if(substr($url, 0, 4) != 'http')
	{
		return 'The URL provided is missing the "http", can not continue with request';
	}
	$req = $url;

	$pos = strpos($req, '://');
	$protocol = strtolower(substr($req, 0, $pos));

	$req = substr($req, $pos + 3);
	$pos = strpos($req, '/');
	if ($pos === false) {
		$pos = strlen($req);
	}
	$host = substr($req, 0, $pos);

	if (strpos($host, ':') !== false) {
		list($host, $port) = explode(':', $host);
	} else {
		$port = ($protocol == 'https') ? 443 : 80;
	}

	$uri = substr($req, $pos);
	if (empty($uri)) {
		$uri = '/';
	}

	$crlf = "\r\n";

	// generate request
	$req = 'GET '.$uri.' HTTP/1.0'.$crlf.'Host: '.$host.$crlf.$crlf;

	error_reporting(E_ERROR);

	// fetch
	$fp = fsockopen((($protocol == 'https') ? 'tls://' : '').$host, $port, $errno, $errstr, 20);
	if (!$fp) {
		return "BADURL";
	}

	fwrite($fp, $req);
	while (is_resource($fp) && $fp && !feof($fp)) {
		$response .= fread($fp, 1024);
	}
	fclose($fp);

	// split header and body
	$pos = strpos($response, $crlf.$crlf);
	if ($pos === false) {
		return($response);
	}
	$header = substr($response, 0, $pos);
	$body = substr($response, $pos + 2 * strlen($crlf));

	// parse headers
	$headers = array();
	$lines = explode($crlf, $header);
	foreach ($lines as $line) {
		if (($pos = strpos($line, ':')) !== false) {
			$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));
		}
	}

	// redirection?
	if (isset($headers['location'])) {
		return hotaru_http_request($headers['location']);
	}

	return $body;
}


 function sendResponse($status = 200, $body = '', $content_type = 'text/html', $file = '')
{
	// set the status
	$status_header = 'HTTP/1.1 ' . $status . ' ' . getStatusCodeMessage($status);
	header($status_header);
	// and the content type
	header('Content-type: ' . $content_type);
        
        if ($file) {
            //get the last-modified-date of this very file
            $lastModified=filemtime($file);
            //get a unique hash of this file (etag)
            $etagFile = md5_file($file);
            
            //set etag-header
            header("Etag: $etagFile");
            //set last-modified header
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
        }

	// pages with body are easy
	if($body != '')
	{
	    // send the body
	    echo $body;
	    exit;
	}
	// we need to create the body if none is passed
	else
	{
	    // create some body messages
	    $message = '';

	    // this is purely optional, but makes the pages a little nicer to read
	    // for your users.  Since you won't likely send a lot of different status codes,
	    // this also shouldn't be too ponderous to maintain
	    switch($status)
	    {
		case 401:
		    $message = 'You must be authorized to view this page.';
		    break;
		case 404:
		    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
		    break;
		case 500:
		    $message = 'The server encountered an error processing your request.';
		    break;
		case 501:
		    $message = 'The requested method is not implemented.';
		    break;
	    }

	    // servers don't always have a signature turned on
	    // (this is an apache directive "ServerSignature On")
	    $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

	    // this should be templated in a real-world solution
	    $body = '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
		<html>
		<head>
		    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		    <title>' . $status . ' ' . getStatusCodeMessage($status) . '</title>
		</head>
		<body>
		    <h1>' . getStatusCodeMessage($status) . '</h1>
		    <p>' . $message . '</p>
		    <hr />
		    <address>' . $signature . '</address>
		</body>
		</html>';

	    echo $body;
	    exit;
	}
    }
    
    
function getStatusCodeMessage($status)
{
    $codes = Array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
    );
    return (isset($codes[$status])) ? $codes[$status] : '';
}

 /**
* Validate domain exists
*
* @param $domain - domain to check
* @param $return host - if true, will return the host name
* $return bool or host string
*/
function verifyDomain($h, $domain, $return_host = true)
{
    // from http://stackoverflow.com/questions/1755144/how-to-validate-domain-name-in-php
    if (!filter_var(gethostbyname($domain), FILTER_VALIDATE_IP) && !filter_var(gethostbyname($domain), FILTER_VALIDATE_URL)) {
        $h->messages[$h->lang('global_functions_error_message_no_such_domain')] = "red";
        return false;
    }

    if ($return_host) {
        $parsed_url = parse_url($domain, PHP_URL_HOST);

        // if it came in just as the domain, we will return that as, otherwise, it will be false
        if (!$parsed_url) { $parsed_url = $domain; }

        $check = is_ip($parsed_url);
        $host = $parsed_url;

        if (!$check) {
          $host = !$host ? domain($host) : domain($domain);
        }

        // this function doesn't work so good - kludge
        if (substr($host, 0, 1) == '.') { $host = substr($host, 1); }

        // we don't need to verify ourselves
        if ($host == SITEURL ) { return $host; }

        // from http://sourceforge.net/projects/phpwhois/?source=dlp
        //include(EXTENSIONS . 'phpwhois/whois.main.php');
        $whois = new Whois();

        $whois->deep_whois = false;
        $result = $whois->Lookup($host,false);

        if (isset($result['regrinfo']['registered']) && $result['regrinfo']['registered'] == 'yes') {
            // if host starts with www, we will remove
            if (substr(strtolower($host), 0, 4) === 'www.') {
                 $host = substr($host, 4);
            }
            return $host;
        } 
        
        $h->messages[$h->lang('global_functions_error_message_no_such_domain')] = "red";
        return false;
    }

    return true;
}


// adopted from http://www.php.net/manual/en/function.parse-url.php#104874
function is_ip($ip_addr)
{
    // first of all the format of the ip address is matched
    if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
        // now all the intger values are separated
        $parts = explode(".", $ip_addr);

        // now we need to check each part can range from 0-255
        foreach ($parts as $ip_parts) {
            //if number is not within range of 0-255
            if (intval($ip_parts) > 255 || intval($ip_parts) < 0) { return false; }
        }
        return true;
    } 
    
    return false;   
}


function domain($domainb)
{
    $bits = explode('/', $domainb);

    if ($bits[0] == 'http:' || $bits[0] == 'https:') {
        $domainb = $bits[2];
    } else {
        $domainb = $bits[0];
    }

    unset($bits);
    $bits = explode('.', $domainb);
    $idz = count($bits);
    $idz -= 3;

    if (strlen($bits[($idz + 2)]) == 2) {
        $bits_idz = isset($bits[$idz]) ? $bits[$idz] :  '';
        $url = $bits_idz . '.' . $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
    } elseif (strlen($bits[($idz + 2)]) == 0) {
         $url = $bits[($idz)] . '.' . $bits[($idz + 1)];
    } else {
        $url = $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
    }

    return $url;
}
