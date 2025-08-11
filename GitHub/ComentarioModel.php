<?php
/**
 * Encargado de manejar operaciones de comentarios en la base de datos.
 */
class ComentarioModel {
    /** @var mysqli $conn Conexión a la base de datos */
    private $conn;

    /**
     * Constructor
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Agrega un nuevo comentario a una receta.
     * 
     * @param int $receta_id ID de la receta
     * @param string $autor Nombre del autor del comentario
     * @param string $contenido Texto del comentario
     * @param string $fecha Fecha del comentario (formato 'Y-m-d H:i:s')
     * @param int $usuario_id ID del usuario que comenta
     * @return bool true si se insertó correctamente, false en caso contrario
     */
    public function agregarComentario($receta_id, $autor, $contenido, $fecha, $usuario_id) {
        $stmt = $this->conn->prepare("
            INSERT INTO comentarios (receta_id, autor, contenido, fecha, usuario_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("isssi", $receta_id, $autor, $contenido, $fecha, $usuario_id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    /**
     * Obtiene todos los comentarios de una receta ordenados por fecha descendente.
     * 
     * @param int $receta_id ID de la receta
     * @return array Lista de comentarios como arrays asociativos
     */
    public function getComentariosPorReceta($receta_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM comentarios
            WHERE receta_id = ?
            ORDER BY fecha DESC
        ");

        $stmt->bind_param("i", $receta_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $comentarios = [];

        while ($row = $result->fetch_assoc()) {
            $comentarios[] = $row;
        }

        $stmt->close();
        return $comentarios;
    }
}
?>
