<?php
	$curl = curl_init();
	$videoId = "aPEqUYpYq2o"; //POST
	curl_setopt($curl, CURLOPT_URL, "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId=aPEqUYpYq2o&key=AIzaSyCIbp9uGZhPTc2yC0z1bSltADLgm7IT_Ds&maxResults=2&textFormat=plainText");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($curl);

	if(curl_errno($curl)){
    	echo 'Curl error: ' . curl_error($curl);
	}
	curl_close($curl);

	$result = json_decode($result,true);
	echo print_r($result);
?>