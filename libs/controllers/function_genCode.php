<?php

function KeyGenerator($size)
  {
    // Initialisation des caractères utilisables
    $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
    $password = '';
    for ($i = 0; $i < $size; $i++) {
      $password .= ($i % 2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
    }

    return $password;
  }

?>