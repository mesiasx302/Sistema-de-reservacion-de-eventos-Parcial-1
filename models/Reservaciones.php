
<?php

class Reservaciones
{
    public function __construct(private PDO $db) {}

    public function todos(): array
    {
        $sql = "
            SELECT
                r.reservacion_id,
                r.fecha_reservacion,

                CASE
                    WHEN r.estado = 'Cancelada' THEN 'Cancelada'

                    WHEN e.fecha_fin IS NOT NULL
                    AND e.fecha_fin < NOW()
                    THEN 'Finalizada'

                    ELSE 'Activa'
                END AS estado,

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

        $sqlVerificar = "
            SELECT COUNT(*)
            FROM Reservaciones
            WHERE evento_id = ?
            AND cliente_id = ?
            AND estado = 'Activa'
        ";

        $stmtVerificar = $this->db->prepare($sqlVerificar);
        $stmtVerificar->execute([$eventoId, $clienteId]);

        if ((int) $stmtVerificar->fetchColumn() > 0) {
            throw new InvalidArgumentException(
                'Este cliente ya tiene una reservación activa para este evento.'
            );
        }


        $sqlInsertar = "
            INSERT INTO Reservaciones (evento_id, cliente_id)
            VALUES (?, ?)
        ";

        return $this->db
            ->prepare($sqlInsertar)
            ->execute([$eventoId, $clienteId]);
    }


    public function cancelar(int $id): bool
    {
        $sql = "
            UPDATE Reservaciones rINNER JOIN Eventos e
                ON e.evento_id = r.evento_id

            SET r.estado = 'Cancelada'

            WHERE r.reservacion_id = ?
            AND r.estado = 'Activa'
            AND (
                    e.fecha_fin IS NULL
                    OR e.fecha_fin >= NOW()
            )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
