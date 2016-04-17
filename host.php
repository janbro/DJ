<!doctype html>
<?php
file_put_contents("skips.txt", "");
if(file("songs-queued.txt")!=NULL){
$songInfo = explode("~",file("songs-queued.txt")[0],3);
}?>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="css/normalize.css" type="text/css">
  <link rel="stylesheet" href="css/skeleton.css" type="text/css">
  <style>.video-container {
	position:relative;
	padding-bottom:56.25%;
	padding-top:30px;
	height:0;
	overflow:hidden;
}

.video-container iframe, .video-container object, .video-container embed {
	position:absolute;
	top:0;
	left:0;
	width:100%;
	height:100%;
}</style>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
  <script src="https://connect.soundcloud.com/sdk/sdk-3.0.0.js"></script>
  <script src="js/search.js"></script>
  <script src="https://w.soundcloud.com/player/api.js"></script>
    <script src="https://www.youtube.com/player_api"></script>
  <script>
  var url;
  var site = "<?php if(file("songs-queued.txt")!=NULL){echo trim(preg_replace('/\s+/', ' ', $songInfo[0]));}?>";
  SC.initialize({
  client_id: '2aa453bf6ef372ab4f7462d76cf01197'
  });
  function removeSong(place){
    $.ajax({
            type: "GET",
            url: "remove.php",
            data: {
              id: place // various ways to store the ID, you can choose
            }
          });
  }
  var player;
  function onPlayerReady(event) {
    event.target.setVolume(100);
    event.target.playVideo();
    event.target.setPlaybackQuality('hd720');
  }
  function onPlayerStateChange(event){
    if (event.data === 0) {
      window.location.replace('next.php');
    }
  }
  function play(){
    switch(site){
      case "SoundCloud":
        SC.oEmbed("<?php if(file("songs-queued.txt")!=NULL){echo trim(preg_replace('/\s+/', ' ', $songInfo[1]));}?>",{auto_play:true,maxheight:166}).then(function(oEmbed){
          $(oEmbed.html).appendTo('#player');
          var element = document.getElementById('player');
          element.getElementsByTagName('iframe')[0].setAttribute('id','widget');
          var widget = SC.Widget('widget');
          widget.bind(SC.Widget.Events.READY, function(player) {
            widget.bind(SC.Widget.Events.FINISH, function(player) {
              console.log("finished");
              window.location.replace('next.php');
            });
          });
        });
        break;
      case "YouTube":
        console.log("yOUtube");
        player = new YT.Player('player', {
        width: 854,
        height: 480,
        style:"display: block;margin-left: auto;margin-right: auto;",
        videoId: "<?php if(file("songs-queued.txt")!=NULL){echo trim(preg_replace('/\s+/', ' ', $songInfo[1]));}?>",
        events: {
          'onReady': onPlayerReady,
          'onStateChange': onPlayerStateChange,
        }
        });
        $('#player').append('style="display: block;margin-left: auto;margin-right: auto;"');
        // document.getElementById('player').setAttribute("class","video-container");
        // $('#player').append('<iframe id="player" style="display: block;margin-left: auto;margin-right: auto;" type="text/html" width="854" height="480" src="http://www.youtube.com/embed/<?php if(file("songs-queued.txt")!=NULL){echo trim(preg_replace('/\s+/', ' ', $songInfo[1]));}?>?enablejsapi=1" frameborder="0"></iframe>');
        break;
    }
    getNextSongs();

    }

    function getMedia() {
    $.ajax({
        url: 'songs-queued.txt?random='+Math.random(),
        dataType: 'text',
        success: function(text) {
          setTimeout(getMedia, 2000);
          console.log(text);
          if(text!=""){
            location.reload();
          }
        }
    })
    }
    var previousText="";
    var lastText="";
    $.ajax({
        url: 'songs-queued.txt',
        dataType: 'text',
        success: function(text) {
          lastText=text;
        }
      });
    function getNextSongs() {

      $.ajax({
          url: 'skips.txt?random='+Math.random(),
          dataType: 'text',
          success: function(text) {
            document.getElementById('skipText').innerHTML = "Skips: "+text.length+'/4';
            if(text.length>=4){
              window.location.replace('next.php');
            }
          }
        });
    $.ajax({
        url: 'songs-queued.txt?random='+Math.random(),
        dataType: 'text',
        success: function(text) {
          setTimeout(getNextSongs, 2000);
          if(text.split('\n')[0]!=lastText.split('\n')[0]){
            location.reload();
          }
          if(text!=previousText){
              $('#nextSongs').empty();
              $('#nextSongs').append("<thead><tr><th>Up Next</th></tr></thead><tbody>");
              var songs = text.split('\n');
              songs.shift();songs.pop(); //remove first and last 'songs'
              var i=1;
              for(var s of songs){
                $('#nextSongs').append("<tr><td>"+s.split("~")[2]+"</td><td><button style='float:right;' onclick='removeSong("+i+");'>remove</button></td></tr>");
                // $('#nextSongs').append("<p>"+s.split("~")[2]+"</p>");
                i++;
              }
              previousText=text;
            }
        }
    })
    }
    // function getSCSongName(songs){
    //   SC.resolve(track_url).then(function(track){
    //     $('#nextSongs').append("<p>"+track.title+"</p>");
    //   });
    //   // .then(function(track){console.log(track.title);$(appendage).appendTo('#nextSongs');});
    // }
  </script>
  <meta charset="utf-8" />
    <title>Play</title>
  </head>
  <?php
  if(file("songs-queued.txt")!=NULL){
  ?>
  <body onload="play();">
    <div class="container">
      <div id="player">
      </div><br>
      <div class="row">
        <div id="skips" class="four columns">
          <h2 id="skipText">Skips:</h2>
        </div>
        <button id="nextButton" class="button-primary" style="float:right;" onclick="window.location.replace('next.php');">Next</button>
        <br>
      </div>
      <table class='u-full-width' id='nextSongs'></table>
    </div>
  </body>
  <?php }else{?>
  <body onload="getMedia();">
    <div class="container">
      <div class="row">
        <div class="twelve columns"><h1 style='text-align:center;'>Queue Song</h1><br><h1 style='text-align:center;'>connect to MessWithTheBest</h1><br><h1 style='text-align:center;'>go to 192.168.2.144/DJ</h1>
      </div>
    </div>
  </body>
  <?php } ?>
</html>
