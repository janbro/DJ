<?php $arr = file("songs-queued.txt");
echo $_GET['id'];
      if (isset($arr[$_GET['id']])){
        unset ($arr[$_GET['id']]);
      }
      $string = implode('', $arr);
      file_put_contents("songs-queued.txt", $string);
 ?>
