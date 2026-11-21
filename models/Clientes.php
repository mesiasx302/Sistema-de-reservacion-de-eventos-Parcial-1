<?php

class Clientes
{
    public function __construct(private PDO $db) {}

    
    public function todos(): array
    {
        return $this->db->query(
            'SELECT * FROM Clientes ORDER BY apellido, nombre'
        )->fetchAll();
    }

    
    public function crear(array $datos): bool
    {
        $sql = 'INSERT INTO Clientes (nombre, apellido, email, telefono)
                VALUES (:nombre, :apellido, :email, :telefono)';

        return $this->db->prepare($sql)->execute($datos);
    }

    
    public function actualizar(int $id, array $datos): bool
    {
        $sql = 'UPDATE Clientes
                SET nombre = :nombre,
                    apellido = :apellido,
                    email = :email,
                    telefono = :telefono
                WHERE cliente_id = :id';

        $datos['id'] = $id;

        return $this->db->prepare($sql)->execute($datos);
    }

    
    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM Clientes WHERE cliente_id = ?'
        );

        return $stmt->execute([$id]);
    }
}