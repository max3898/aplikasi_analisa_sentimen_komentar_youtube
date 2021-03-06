<?php
	require_once('../../mungkin_stemmed/NaiveBayesClassifier.php');

    $classifier = new NaiveBayesClassifier();
    $Positif = Category::$Positif;
    $Negatif = Category::$Negatif;
    $bukanBahasaIndonesia = Category::$bukanBahasaIndonesia;

	$curl = curl_init();
	$videoId = $_POST['videoId']; //POST
	curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId='.$videoId.'&key=AIzaSyCIbp9uGZhPTc2yC0z1bSltADLgm7IT_Ds&maxResults=40&textFormat=plainText');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($curl);

	if(curl_errno($curl)){
    	echo 'Curl error: ' . curl_error($curl);
	}
	curl_close($curl);

	$result = json_decode($result,true);
	
	$hasil = array();
	$hasil['list_positif'] = "";
	$hasil['list_negatif'] = "";
	$hasil['list_bukan_bahasa_indonesia'] = "";
	foreach($result['items'] as $val) {
      $raw_comment = preg_replace('/\s+/', ' ', $val['snippet']['topLevelComment']['snippet']['textDisplay']);
      $comment = preg_replace("/[^A-Za-z\  ]/", "", $raw_comment); //Get Comment Content

      $category = $classifier -> classify($comment);
      $result['items']['snippet']['topLevelComment']['snippet']['sentiment'] = $category;
    }
    echo json_encode($hasil);
?>