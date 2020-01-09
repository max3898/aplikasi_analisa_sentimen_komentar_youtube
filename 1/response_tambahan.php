<?php
	
	require_once('../mungkin/NaiveBayesClassifier.php');

    $classifier = new NaiveBayesClassifier();
    $Positif = Category::$Positif;
    $Negatif = Category::$Negatif;
    $bukanBahasaIndonesia = Category::$bukanBahasaIndonesia;

	$curl = curl_init();
	$videoId = $_POST['videoId']; //POST
	$url = $_POST['url'];
	curl_setopt($curl, CURLOPT_URL, $url);
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

      $author = $val['snippet']['topLevelComment']['snippet']['authorDisplayName']; //Get Comment Author Name.
      $author_url = $val['snippet']['topLevelComment']['snippet']['authorChannelUrl']; //Get Comment Author URL.
      $author_thumbnail_url = $val['snippet']['topLevelComment']['snippet']['authorProfileImageUrl']; //Get Comment Author Thumbnail URL.
      $raw_comment = preg_replace('/\s+/', ' ', $val['snippet']['topLevelComment']['snippet']['textDisplay']);
      $comment = preg_replace("/[^A-Za-z\  ]/", "", $raw_comment); //Get Comment Content.

      $category = $classifier -> classify($comment);
      $time = "";
      $tanggal = "2019-11-20T06:40:49.000Z";
		$tanggal = (explode("T",$tanggal));
		$diff=date_diff(date_create($tanggal[0]),date_create(date("Y-m-d")));
		if ($diff->format("%a days") == "0 days") {
			$time = "Today";
		}
		elseif ($diff->format("%a days") == "1 days") {
			$time = "Yesterday";
		}
		else{
			$time = $diff->format("%a days");
		}
      if ($category == "Positif") {
      	$hasil['list_positif'] = $hasil['list_positif'].'<li class="media"><a href="#" class="pull-left"><img src="'.$author_thumbnail_url.'" alt="" class="img-circle" style="width: 48px; height: 48px;"></a><div class="media-body"><span class="text-muted pull-right" style="float: right;"><small class="text-muted"></small></span><strong class="text-success"><a href="'.$author_url.'" target="_blank">'.$author.'</a></strong><p>'.$comment.' </a>.</p></div></li>';
      }
      else if($category == "Negatif"){
      	$hasil['list_negatif'] = $hasil['list_negatif'].'<li class="media"><a href="#" class="pull-left"><img src="'.$author_thumbnail_url.'" alt="" class="img-circle" style="width: 48px; height: 48px;"></a><div class="media-body"><span class="text-muted pull-right" style="float: right;"><small class="text-muted"></small></span><strong class="text-success"><a href="'.$author_url.'" target="_blank">'.$author.'</a></strong><p>'.$comment.' </a>.</p></div></li>';
      }
      else{
      	$hasil['list_bukan_bahasa_indonesia'] = $hasil['list_bukan_bahasa_indonesia'].'<li class="media"><a href="#" class="pull-left"><img src="'.$author_thumbnail_url.'" alt="" class="img-circle" style="width: 48px; height: 48px;"></a><div class="media-body"><span class="text-muted pull-right" style="float: right;"><small class="text-muted"></small></span><strong class="text-success"><a href="'.$author_url.'" target="_blank">'.$author.'</a></strong><p>'.$comment.' </a>.</p></div></li>';
      }
    }
    $hasil['data'] = 'https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId='.$videoId.'&key=AIzaSyCIbp9uGZhPTc2yC0z1bSltADLgm7IT_Ds&maxResult=40&textFormat=plainText&pageToken='.$result['nextPageToken'];
    // var_dump($hasil);
    echo json_encode($hasil);
?>