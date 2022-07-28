<?php
    function exists($var, $array) {
        return array_key_exists($var, $array) && isset($array[$var]) && !(empty($array[$var]));
    }
?>