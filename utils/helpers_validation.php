<?php
    /**
     * Software Development SAT - Pavilion Events Management System (PEMS)
     *
     * Functions for the validation of form inputs.
     *
     * @author Finn Scicluna-O'Prey <finn@oprey.co>
     *
     */

    function exists($var, $array) {
        return array_key_exists($var, $array) && isset($array[$var]) && !(empty($array[$var]));
    }
?>