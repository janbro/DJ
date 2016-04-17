// After the API loads, call a function to enable the search box.
function handleAPILoaded() {
  $('#search-button').attr('disabled', false);
}
function animate(i){
  console.log(i);
  $('widget'+i).animate({paddingRight:'0%'});
}
// Search for a specified string.
function search() {
  var q = $('#query').val();
  $('#search-container').empty();
  var site=$('#search-site option:selected').html();
  switch(site){
    case 'YouTube':
      //Search Youtube
      var request;
      gapi.client.setApiKey('YOUR-API-KEY');
      gapi.client.load('youtube', 'v3').then(function() {
        request = gapi.client.youtube.search.list({
          q: q, //old one was without quotes
          part: 'id, snippet', //the parts should be in quotes as well
          type: 'video',
          order: 'relevance',
          maxResults: '5',
          videoEmbeddable: 'true'
        });
        request.execute(function(response) {
          var str = JSON.stringify(response.result);
          for(var i=0;i<response.result.items.length;i++){
            // $("<p>"+response.result.items[i].snippet.title+"</p>").appendTo('#search-container');
            //document.getElementById('search-container').setAttribute("class","video-container");
            $('#search-container').append('<iframe id="player" style="paddingRight:2000px" style="display: block;margin-left: auto;margin-right: auto;" type="text/html" width="854" height="480" src="http://www.youtube.com/embed/'+response.result.items[i].id.videoId+'?enablejsapi=1" frameborder="0"></iframe><br>');
            $('<form action="http://192.168.2.144/DJ/queue.php" method="get"><input type="hidden" name="title" value="'+response.result.items[i].snippet.title+'"><input type="hidden" name="permalink" value="'+response.result.items[i].id.videoId+'"><input type="hidden" name="site" value="'+site+'"><button id="queue_track" type="submit" style="float:right;" onclick="">Send to Queue</button></form>').appendTo('#search-container');
          }
        });
      });
      break;
    case 'SoundCloud':
      //Search SoundCloud
      SC.initialize({
        client_id: 'YOUR-CLIENT-KEY'
      });

      // find all sounds of buskers licensed under 'creative commons share alike'
      SC.get('/tracks', {
        q: q,
        limit: 10,
      }).then(function(tracks) {
        var none=true;
        for(var i=0;i<tracks.length;i++){
          //$("<p><a href="+tracks[i].permalink_url+">"+tracks[i].title+"</a></p>").appendTo('#search-container');
          //$("<img src="+tracks[i].artwork_url+" alt="+tracks[i].id+">").appendTo('#search-container');
          if(tracks[i].embeddable_by=='all'){
            var track_url = tracks[i].permalink_url;
            document.getElementById('search-container').setAttribute("class","");
            $('<div id="widget'+i+'" style="padding-right:0%"></div>').appendTo('#search-container');
            SC.oEmbed(track_url,{element: document.getElementById('widget'+i),auto_play:false,maxheight:166});
            console.log($('#widget'+i).children()[0]);
              // $(oEmbed.html).appendTo('#search-container');
              // console.log(tracks[i].permalink_url);
              // console.log($('#search-site option:selected').html());
              none=false;
              $('<form action="http://192.168.2.144/DJ/queue.php" method="get"><input type="hidden" name="title" value="'+tracks[i].title+'"><input type="hidden" name="permalink" value="'+track_url+'"><input type="hidden" name="site" value="'+site+'"><button id="queue_track" type="submit" style="float:right;" onclick="">Send to Queue</button></form>').appendTo('#search-container');
            }
          }
          if(none){
            $('#search-container').append('No results found!');
          }
        });
      break;
    case 'Spotify':
      //Search Spotify
      break;
  }
}
