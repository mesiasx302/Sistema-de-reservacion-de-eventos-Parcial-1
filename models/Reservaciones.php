
<?php

class Reservaciones
{
    public function __construct(private PDO $db) {}

    private function finalizarVencidas(): void
    {
        $sql = "
            UPDATE Reservaciones r

            INNER JOIN Eventos e
                ON e.evento_id = r.evento_id

            SET r.estado = 'Finalizada'

            WHERE r.estado = 'Activa'
            AND e.fecha_fin IS NOT NULL
            AND e.fecha_fin <= NOW()
        ";

        $this->db->exec($sql);
    }

    public function todos(): array
    {
        $this->finalizarVencidas();

        $sql = "
            SELECT
                r.reservacion_id,
                r.evento_id,
                r.fecha_reservacion,
                r.estado,

                e.nombre AS evento,
                e.fecha AS fecha_evento,
                e.fecha_fin AS fecha_fin_evento,

                c.nombre,
                c.apellido,
                c.email

            FROM Reservaciones r

            INNER JOIN Eventos e
                ON e.evento_id = r.evento_id

            INNER JOIN Clientes c
                ON c.cliente_id = r.cliente_id

            ORDER BY r.fecha_reservacion DESC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(int $eventoId, int $clienteId): bool
    {
        $this->finalizarVencidas();

        $sqlVerificar = "
            SELECT COUNT(*)
            FROM Reservaciones
            WHERE evento_id = ?
            AND estado = 'Activa'
        ";

        $stmtVerificar = $this->db->prepare($sqlVerificar);
        $stmtVerificar->execute([$eventoId]);

        if ((int) $stmtVerificar->fetchColumn() > 0) {
            throw new InvalidArgumentException(
                'Este evento ya tiene un cliente asignado. No se puede asignar otro mientras la reservación esté activa.'
            );
        }

        $sqlInsertar = "
            INSERT INTO Reservaciones (
                evento_id,
                cliente_id,
                estado
            )
            VALUES (?, ?, 'Activa')
        ";

        return $this->db
            ->prepare($sqlInsertar)
            ->execute([$eventoId, $clienteId]);
    }

    public function cancelar(int $id): bool
    {
        $this->finalizarVencidas();

        $sql = "
            UPDATE Reservaciones

            SET estado = 'Cancelada'

            WHERE reservacion_id = ?
            AND estado = 'Activa'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
