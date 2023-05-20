<?php

class User
{

    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getUserByLogin($login)
    {
        $this->db->query('SELECT * FROM users WHERE login=:login');
        $this->db->bind(':login', $login);
        $row = $this->db->single();
        return $row;
    }

    public function create($data) {
        $password_hash = password_hash($data['password'],PASSWORD_DEFAULT);
        $this->db->query('INSERT INTO users (`login`, `password_hash`) VALUES (:login, :password_hash)');
        $this->db->bind(':login', $data['login']);
        $this->db->bind(':password_hash', $password_hash);

        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }

    }
}