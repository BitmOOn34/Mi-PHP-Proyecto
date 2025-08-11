<?php
include 'db.php';

class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Verifica si el email ya existe en la base de datos
    public function existeEmail($email) {
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $existe = $res->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    // Registra un nuevo usuario con contraseña hasheada
    public function registrarUsuario($nombre, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
        $stmt->bind_param("sss", $nombre, $email, $passwordHash);
        if (!$stmt->execute()) {
            throw new Exception("Error al registrar usuario: " . $stmt->error);
        }
        $insertId = $stmt->insert_id;
        $stmt->close();
        return $insertId;
    }
}
