<?php

Flight::before("start", function(){
  $delay = get_config("delay", 0);
  if($delay) {
    sleep($delay);
  }
});

?>
