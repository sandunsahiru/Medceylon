<<<<<<< HEAD
<?php
    class M_Pages {
        private $db;
        public function __construct() {
                $this->db = new Database();
        }

        public function getUsers() {
            $this->db->query("SELECT * FROM Users");

            return $this->db->resultSet();
        }
    }
=======
<?php
    class M_Pages {
        private $db;
        public function __construct() {
                $this->db = new Database();
        }

        public function getUsers() {
            $this->db->query("SELECT * FROM Users");

            return $this->db->resultSet();
        }
    }
>>>>>>> d7fee2e90c0e8b6767e13b75b1ecae8294eab4cf
?>