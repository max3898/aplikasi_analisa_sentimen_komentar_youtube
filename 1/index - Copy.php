<!DOCTYPE html>
<html style="padding:2%;">
<head>
	<meta charset="UTF-8">
  	<meta name="description" content="">
  	<meta name="keywords" content="">
  	<meta name="author" content="Maximillian Christianto">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Analisa Komentar Youtube</title>
	<script src="../jquery-3.4.1.min.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<style type="text/css">
		body{
			/* The image used */
			  background-image: url("../background.jpg");

			  /* Full height */
			  height: 100%;

			  /* Center and scale the image nicely */
			  background-position: center;
			  background-repeat: repeat;
			  background-size: cover;
		}
		.konten{
			border-radius: 3px;
		}
		.comment-wrapper{
			padding: 0;
		}
		.panel-body{
			padding-top: 0px;
			margin-top: 0px;
		}
		.rubahHeader{
			position: -webkit-sticky;
			position: sticky;
			top: 0;
			padding: 0%;
			margin: 0%;
		}
		.awal{
			background-color: #C0C0C0;
			padding: 5%;
			margin-bottom: 	0;
			margin-top: 0;
		}
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row align-items-center" style="margin: 2%;">
	  		<div class="col-12" align="center">
			  <div class="input-group">
			    	<input id="urlVideo" type="text" class="form-control" placeholder="Search">
				    <div class="input-group-btn">
				      <button onclick="getIdVideo()" class="btn btn-primary" type="submit">
				        <i class="glyphicon glyphicon-search"></i>
				      </button>
				    </div>
			  </div>
			  <div id="errorAlert" class="alert alert-danger alert-dismissible hide">
			    <button type="button" class="close" data-dismiss="alert">&times;</button>
			    <strong>Please</strong> insert link video.
			  </div>
		    </div>
		</div>
	  <div class="row">
	    <div class="col-md-4 konten">
	      <h4 class="awal" style="text-align: center;">Sentimen Positif</h4>
	      <div class="comment-wrapper">
            <div class="panel panel-info">
                <div class="panel-body">
                    <br>
                    <div class="clearfix"></div>
                    <ul class="media-list list-positif">
                    	<li style="display: hidden;"></li>
                    </ul>
                </div>
            </div>
        </div>
	    </div>
	    <div class="col-md-4 konten">
	      <h4 class="awal" style="text-align: center;">Sentimen Negatif</h4>
	      <div class="comment-wrapper">
            <div class="panel panel-info">
                <div class="panel-body">
                    <br>
                    <div class="clearfix"></div>
                    <ul class="media-list list-negatif">
                    </ul>
                </div>
            </div>
	    </div>
	</div>
	<div class="col-md-4 konten">
	      <h4 class="awal" style="text-align: center;">Bukan Berbahasa Indonesia</h4>
	      <div class="comment-wrapper">
            <div class="panel panel-info">
                <div class="panel-body">
                    <br>
                    <div class="clearfix"></div>
                    <ul class="media-list list-bukan-bahasa-indonesia">
                    </ul>
                </div>
            </div>
	    </div>
	</div>
</body>
</html>

<script>
	var list_positif = "";
	var list_negatif = "";
	var list_bukan_bahasa_indonesia = "";
	var url = "";
	var videoId = "";
	function pickData(){
		if (url == "") {
			$.ajax({
				url:"http://localhost/skripsi/1/response.php",
				method:"POST",
				data:{videoId:videoId},
				success:function(data)
				{
					var obj = jQuery.parseJSON(data);
					$(".list-positif").append(obj.list_positif);
					$(".list-negatif").append(obj.list_negatif);
					$(".list-bukan-bahasa-indonesia").append(obj.list_bukan_bahasa_indonesia);
					nextPageToken=obj.nextPageToken;
					loadData();
					url = obj.data;
				}
			});
		}
	}

	function loadData(){
		$.ajax({
		url:"http://localhost/skripsi/1/response_tambahan.php",
		method:"POST",
		data:{url:url,videoId:videoId},
		success:function(data)
		{
			var obj = jQuery.parseJSON(data);
			list_positif = obj.list_positif;
			list_negatif = obj.list_negatif;
			list_bukan_bahasa_indonesia = obj.list_bukan_bahasa_indonesia;
			url = obj.data;
		}
	});
	}

	function loadDataAgain(){
		$(".list-positif").append(list_positif);
		$(".list-negatif").append(list_negatif);
		$(".list-bukan-bahasa-indonesia").append(list_bukan_bahasa_indonesia);
		list_positif = "";
		list_negatif = "";
		list_bukan_bahasa_indonesia = "";
		loadData();
	}

	$(window).scroll(function () {
	   if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
	      loadDataAgain();
	   }
	});

	if ($(window).width() > 960) {
		$(window).scroll(function() {    

	    var scroll = $(window).scrollTop();

	    if (scroll >= 150) {
	        $(".awal").addClass("rubahHeader");
	    } else {
	        $(".awal").removeClass("rubahHeader");   
	    }
	});
	}

	function getIdVideo(){
		linkVideo = $('#urlVideo').val();
		var res = linkVideo.split("watch?v=");
		videoId = res[1];
		pickData();
		linkVideo = $('#urlVideo').val("");
	}

	$("#urlVideo").keyup(function(event) {
		$(".list-positif").html('');
		$(".list-negatif").html('');
		$(".list-bukan-bahasa-indonesia").html('');
	    if (event.keyCode == 13) {
	    	if ($('#urlVideo').val() != "") {
	    		getIdVideo();
	    	}
	    	else{
	    		 $('#passwordsNoMatchRegister').hide();
	    	}
	    }
	});
</script>