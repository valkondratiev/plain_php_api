<?php

class Item {

    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getItemById($id) {
        $this->db->query('SELECT * FROM items WHERE id=:id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row;
    }
}