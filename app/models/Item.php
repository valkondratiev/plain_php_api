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

    public function addItem($data) {
        $this->db->query('INSERT INTO items (`name`, `phone`, `key`) VALUES (:name, :phone, :key)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':key', $data['key']);

        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}