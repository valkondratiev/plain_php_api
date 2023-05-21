<?php

class History {
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function addHistory($data) {
        $this->db->query('INSERT INTO history (`text`, `item_id`) VALUES (:text, :item_id)');
        $this->db->bind(':text', $data['text']);
        $this->db->bind(':item_id', $data['item_id']);
        $this->db->execute();
    }

}