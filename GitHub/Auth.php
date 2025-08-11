<?php
/**
 * Encargada de la autenticación de usuarios, manejo de sesión y control de acceso.
 */
class Auth {
    private $conn;

    /**
     * Constructor
     */
    public function __construct($conn = null) {
        $this->conn = $conn;

        // Inicia sesión si aún no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Inicia sesión con correo y contraseña
     * @param string $email Correo del usuario
     * @param string $password Contraseña en texto plano
     * @return bool true si el inicio de sesión es exitoso, false en caso contrario
     * @throws Exception si no hay conexión con la base de datos
     */
    public function login($email, $password) {
        if (!$this->conn) {
            throw new Exception("No hay conexión a la base de datos.");
        }

        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($usuario = $res->fetch_assoc()) {
            if (password_verify($password, $usuario['password'])) {
                // Guardar datos del usuario en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = 'usuario';
                return true;
            }
        }

        return false;
    }

    /**
     * Inicia sesión como invitado
     * No requiere autenticación ni conexión a la base de datos
     */
    public function loginInvitado() {
        $_SESSION['rol'] = 'invitado';
        $_SESSION['usuario_id'] = null;
        $_SESSION['usuario_nombre'] = 'Invitado';
    }

    /**
     * Cierra sesión y limpia toda la información del usuario
     */
    public function logout() {
        // Asegura que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpia todas las variables de sesión
        $_SESSION = [];

        // Elimina la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destruye la sesión
        session_destroy();
    }

    /**
     * Verifica si el usuario ha iniciado sesión
     * @return bool true si hay sesión iniciada, false si no
     */
    public function isLoggedIn() {
        return isset($_SESSION['usuario_id']);
    }
}
?>
