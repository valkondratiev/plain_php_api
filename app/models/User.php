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

    public function updateTokenInfo($user_id, $jti, $exp) {
        $this->db->query('UPDATE users SET `jti` = :jti, `expired` = :exp WHERE id= :id');

        $time = new DateTime();
        $time->setTimestamp($exp);

        $this->db->bind(':id', $user_id);
        $this->db->bind(':jti', $jti);
        $this->db->bind(':exp', $time->format('Y-m-d H:i:s'));
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function checkJti($jti) {
        $this->db->query('SELECT * FROM users WHERE jti=:jti');
        $this->db->bind(':jti', $jti);
        $row = $this->db->single();
        return $row;
    }
}