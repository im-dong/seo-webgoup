<?php
class Industry {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getIndustries(){
        $this->db->query('SELECT * FROM industries ORDER BY name ASC');
        return $this->db->resultSet();
    }
}
