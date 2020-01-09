<?php
	require_once('../mungkin_stemmed/NaiveBayesClassifier.php');

    $classifier = new NaiveBayesClassifier();
    $Positif = Category::$Positif;
    $Negatif = Category::$Negatif;
    $bukanBahasaIndonesia = Category::$bukanBahasaIndonesia;

	$curl = curl_init();
	$videoId = $_GET['videoId']; //POST
	curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId='.$videoId.'&key=AIzaSyCIbp9uGZhPTc2yC0z1bSltADLgm7IT_Ds&maxResults=40&textFormat=plainText');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($curl);

	if(curl_errno($curl)){
    	echo 'Curl error: ' . curl_error($curl);
	}
	curl_close($curl);

	$result = json_decode($result,true);
	
	$hasil = array();
	$i = 0;
	foreach($result['items'] as $val) {
      $raw_comment = preg_replace('/\s+/', ' ', $val['snippet']['topLevelComment']['snippet']['textDisplay']);
      $comment = preg_replace("/[^A-Za-z\  ]/", "", $raw_comment); //Get Comment Content

      $category = $classifier -> classify($comment);
      $hasil[$i]['author'] = $val['snippet']['topLevelComment']['snippet']['authorDisplayName'];
      $hasil[$i]['author_url'] = $val['snippet']['topLevelComment']['snippet']['authorChannelUrl'];
      $hasil[$i]['author_thumbnail_url'] = $val['snippet']['topLevelComment']['snippet']['authorDisplayName'];
      $hasil[$i]['category'] = $category;
      $i++;
    }
    // var_dump($hasil);
    echo json_encode($hasil);
?>