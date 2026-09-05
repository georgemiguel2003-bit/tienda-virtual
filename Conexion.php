<?php
/**
 * Conexión a la base de datos usando Class (PDO)
 * Patrón Singleton: una sola instancia por petición
 */
class Conexion {
    private static $instancia = null;
    private $pdo;

    private string $host   = 'localhost';
    private string $dbname = 'tienda_virtual';
    private string $user   = 'root';
    private string $pass   = '';

    // Constructor privado → no se puede instanciar desde afuera
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['exito' => false, 'mensaje' => 'Error de conexión: ' . $e->getMessage()]);
            exit;
        }
    }

    // Obtener instancia única
    public static function obtener(): PDO {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia->pdo;
    }
}