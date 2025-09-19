<?php
class Industry {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getIndustries(){
        $this->db->query('SELECT * FROM industries ORDER BY name ASC');
        $industries = $this->db->resultSet();

        // 找到"Unspecified"记录并移到列表开头
        foreach ($industries as $key => $industry) {
            if ($industry->name === 'Unspecified') {
                $unspecified = $industry;
                unset($industries[$key]);
                array_unshift($industries, $unspecified);
                break;
            }
        }

        return array_values($industries);
    }
}
