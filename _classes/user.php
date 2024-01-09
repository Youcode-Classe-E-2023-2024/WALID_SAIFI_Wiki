<?php

class User {
    private $id;
    private $username;
    private $password;
    private $email;

    public function __construct($id) {
        global $db;
        $sql = "SELECT * FROM user WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $user['id'];
        $this->username = $user['username'];
        $this->email = $user['email'];
        $this->password = $user['password'];
    }

    static public function getId($email) {
        global $db;
        $sql = "SELECT id FROM user WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['id']) {
            return $row['id'];
        }
        return NULL;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    static public function register($username, $password, $email) {
        global $db;
        $result = $db->query("SELECT COUNT(*) as total FROM user");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $totalUsers = $row['total'];

        $id_role = ($totalUsers === '0') ? 1 : 2;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (username, password, email, role_id) VALUES (:username, :password, :email, :id_role)";
        $insert = $db->prepare($sql);
        $insert->bindParam(':username', $username, PDO::PARAM_STR);
        $insert->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $insert->bindParam(':email', $email, PDO::PARAM_STR);
        $insert->bindParam(':id_role', $id_role, PDO::PARAM_INT);

        $insert->execute();
    }

    static public function login($enteredPassword, $email) {
        global $db;
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $hashedPassword = $result['password'];
            return password_verify($enteredPassword, $hashedPassword);
        }

        return false;
    }

    static public function logout() {
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }

    static public function getAll() {
        global $db;
        $result = $db->query("SELECT * FROM user");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}