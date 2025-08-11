<?php

class RecetaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Buscar recetas por nombre o ingredientes
    public function buscarRecetas($filtro = '') {
        $filtro_sql = "%$filtro%";
        $stmt = $this->conn->prepare("
            SELECT id, nombre, ingredientes, instrucciones, tiempo_preparacion, porciones, categoria, imagen, calificacion, usuario_id 
            FROM recetas 
            WHERE nombre LIKE ? OR ingredientes LIKE ?
        ");
        $stmt->bind_param("ss", $filtro_sql, $filtro_sql);
        if (!$stmt->execute()) {
            throw new Exception("Error al buscar recetas: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $recetas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $recetas;
    }

    // Agregar nueva receta
    public function agregarReceta($usuario_id, $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen = null) {
        $stmt = $this->conn->prepare("
            INSERT INTO recetas (usuario_id, nombre, ingredientes, instrucciones, tiempo_preparacion, porciones, categoria, imagen) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        // Tipos: i = int, s = string
        $stmt->bind_param("issssiss", $usuario_id, $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen);
        if (!$stmt->execute()) {
            throw new Exception("Error al agregar receta: " . $stmt->error);
        }
        $stmt->close();
    }

    // Verificar si un usuario es propietario de una receta
    public function usuarioEsPropietario($id, $usuario_id) {
        $stmt = $this->conn->prepare("SELECT usuario_id FROM recetas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($owner_id);
        $stmt->fetch();
        $stmt->close();
        return $owner_id == $usuario_id;
    }

    // Actualizar una receta, eliminando imagen anterior si hay nueva
    public function actualizarReceta($id, $usuario_id, $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen = null) {
        // Obtener imagen actual para eliminar si se reemplaza
        $imagenAnterior = null;
        if ($imagen) {
            $stmt = $this->conn->prepare("SELECT imagen FROM recetas WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($imagenAnterior);
            $stmt->fetch();
            $stmt->close();
        }

        if ($imagen) {
            $stmt = $this->conn->prepare("
                UPDATE recetas 
                SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=?, imagen=?
                WHERE id=? AND usuario_id=?
            ");
            $stmt->bind_param("ssssissii", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen, $id, $usuario_id);
        } else {
            $stmt = $this->conn->prepare("
                UPDATE recetas 
                SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=?
                WHERE id=? AND usuario_id=?
            ");
            $stmt->bind_param("ssssisii", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $id, $usuario_id);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar receta: " . $stmt->error);
        }
        $stmt->close();

        // Borrar imagen anterior si se actualizó con una nueva
        if ($imagen && $imagenAnterior && file_exists("uploads/" . $imagenAnterior)) {
            unlink("uploads/" . $imagenAnterior);
        }
    }

    // Eliminar receta y su imagen (con seguridad de usuario propietario)
    public function eliminarReceta($id, $usuario_id) {
        // Confirmar propietario
        if (!$this->usuarioEsPropietario($id, $usuario_id)) {
            throw new Exception("No tienes permiso para eliminar esta receta.");
        }

        // Obtener nombre imagen para borrar archivo
        $stmt = $this->conn->prepare("SELECT imagen FROM recetas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            if (!empty($row['imagen']) && file_exists("uploads/" . $row['imagen'])) {
                unlink("uploads/" . $row['imagen']);
            }
        }
        $stmt->close();

        // Eliminar receta
        $stmt = $this->conn->prepare("DELETE FROM recetas WHERE id = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar receta: " . $stmt->error);
        }
        $stmt->close();
    }

    // Manejar imagen subida: valida extensión y mueve archivo
    public function manejarImagenSubida($file, $uploadDir) {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = basename($file['name']);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowed)) {
                throw new Exception("Tipo de archivo no permitido.");
            }

            $nuevoNombre = uniqid('img_') . '.' . $ext;
            $destino = rtrim($uploadDir, '/') . '/' . $nuevoNombre;

            if (move_uploaded_file($fileTmpPath, $destino)) {
                return $nuevoNombre;
            } else {
                throw new Exception("Error al mover la imagen subida.");
            }
        }
        return null;
    }
}
