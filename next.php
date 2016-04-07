<?php $arr = file("songs-queued.txt");
      if (isset($arr[0])){
        unset ($arr[0]);
      }
      $string = implode('', $arr);
      file_put_contents("songs-queued.txt", $string);
      header("Location: host.php");
?>
