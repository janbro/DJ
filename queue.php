<?php
if($_GET["site"]!=""&&$_GET["permalink"]!=""){
  file_put_contents("songs-queued.txt",$_GET["site"],FILE_APPEND);
  file_put_contents("songs-queued.txt","~",FILE_APPEND);
  file_put_contents("songs-queued.txt",$_GET["permalink"],FILE_APPEND);
  file_put_contents("songs-queued.txt","~",FILE_APPEND);
  file_put_contents("songs-queued.txt",$_GET["title"],FILE_APPEND);
  file_put_contents("songs-queued.txt","\n",FILE_APPEND);
}
header('Location: /DJ');
 ?>
