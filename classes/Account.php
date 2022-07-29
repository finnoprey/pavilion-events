<?php
    class Account {
        /** @var string $username The account username, often an email **/
        public $username;
        /** @var string $type The type of the users account **/
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