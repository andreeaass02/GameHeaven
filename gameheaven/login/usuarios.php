<?php
require_once("../config.php");

class Usuario {
    protected $conn;

    public function __construct() {
        try {
            $this->conn = new PDO(BBDD_DSN, BBDD_USER, BBDD_PASS);
        } catch (PDOException $e) {
            die("ERROR " . $e->getMessage());
        }
    }

    public function __destruct() {
        $this->conn = null;
    }

    public function verificar($usuario, $contra) {
        $query = "SELECT id_usuario, nombre, contrasena, rol FROM usuarios WHERE nombre = :usuario AND contrasena = :contra";
        $stmt = $this->conn->prepare($query);
        $parametros = [':usuario' => $usuario, ':contra' => $contra];
        $stmt->execute($parametros);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            session_start();
            $_SESSION["id_usuario"] = $user['id_usuario'];
            $_SESSION["usuario"] = $user['nombre'];
            $_SESSION["rol"] = $user['rol'];  // Guardar el rol del usuario en la sesión
            header("Location: ../index.php");
        } else {
            echo "<div class='mensaje-error'>El usuario o la contraseña son incorrectos.</div>";
        }

        $stmt = null;
    }
}
?>
