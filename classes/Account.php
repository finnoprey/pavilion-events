<?php
    class Account {
        public $username;
        public $type;

        function __construct(string $username, string $type) {
            $this->username = $username; 
            $this->type = $type;
        }

        function set_username(string $username) {
            $this->username = $username;
        }

        function set_type(string $type) {
            $this->type = $type;
        }
    }
?>