<?php
  function redirect($url) {
    header("Location: " . $url);
  }

  function generate_multiline_string($array) {
    $string = '';
    foreach($array as $line) {
      $string = $string . $line . PHP_EOL;
    }
    return $string;
  }
?>