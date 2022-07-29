<?php
    /**
     * Software Development SAT - Pavilion Events Management System (PEMS)
     *
     * General purpose helper functions for use throughout the solution.
     *
     * @author Finn Scicluna-O'Prey <finn@oprey.co>
     *
     */

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