
<?php

class Eventos
{
    public function __construct(private PDO $db) {}

    
    public function todos(): array
    {
        $sql = 'SELECT * FROM Eventos ORDER BY fecha DESC';

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function vigentes(): array
    {
        $sql = 'SELECT * FROM Eventos
                WHERE fecha_fin IS NULL OR fecha_fin >= NOW()
                ORDER BY fecha ASC';

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function finalizados(): array
    {
        $sql = 'SELECT * FROM Eventos
                WHERE fecha_fin IS NOT NULL
                AND fecha_fin < NOW()
                ORDER BY fecha_fin DESC';

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT * FROM Eventos WHERE evento_id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        return $evento ?: null;
    }

    
    public function crear(array $datos): bool
    {
        $sql = 'INSERT INTO Eventos
                (nombre, descripcion, fecha, fecha_fin, ubicacion)
                VALUES
                (:nombre, :descripcion, :fecha, :fecha_fin, :ubicacion)';

        return $this->db->prepare($sql)->execute($datos);
    }


    public function actualizar(int $id, array $datos): bool
    {
        $sql = 'UPDATE Eventos SET
                    nombre = :nombre,
                    descripcion = :descripcion,
                    fecha = :fecha,
                    fecha_fin = :fecha_fin,
                    ubicacion = :ubicacion
                WHERE evento_id = :id';

        $datos['id'] = $id;

        return $this->db->prepare($sql)->execute($datos);
    }


    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM Eventos WHERE evento_id = ?'
        );

        return $stmt->execute([$id]);
    }
}
