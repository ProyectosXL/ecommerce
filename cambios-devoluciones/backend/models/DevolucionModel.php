<?php
/**
 * Modelo: DevolucionModel
 * Gestiona todas las operaciones de base de datos para el módulo
 * de Logística Inversa (devoluciones y cambios de pedidos).
 *
 * Reutiliza la clase Conexion del sistema central.
 */

require_once __DIR__ . '/../../../Class/Conexion.php';
require_once __DIR__ . '/../../../Class/Pedido.php';

class DevolucionModel
{
    /** @var resource Conexión sqlsrv activa */
    private $conn;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->conn = $conexion->conectarSql('central');

        if (!$this->conn) {
            throw new RuntimeException('No se pudo conectar a la base de datos central.');
        }
    }

    // -------------------------------------------------------
    // CONSULTA DE PEDIDOS EXISTENTES
    // -------------------------------------------------------

    /**
     * Busca un pedido en el sistema existente.
     * Usa los SPs ya existentes con un rango de fecha amplio.
     *
     * @param string $pedidoId  Número de pedido / orden
     * @return array|null       ['cabecera' => ..., 'detalle' => ...]
     */
    public function buscarPedidoExistente(string $pedidoId): ?array
    {
        $pedidoEsc = str_replace("'", "''", trim($pedidoId));
        $hasta     = date('Y-m-d');
        $desde     = date('Y-m-d', strtotime('-2 years'));

        // Cabecera
        $sqlCab = "SET DATEFORMAT YMD;
                   EXEC RO_SP_ECOMMERCE_PEDIDOS_FLUJO '$desde', '$hasta', '$pedidoEsc'";
        $resultCab = sqlsrv_query($this->conn, $sqlCab);

        if ($resultCab === false) {
            error_log('buscarPedidoExistente - cabecera error: ' . print_r(sqlsrv_errors(), true));
            return null;
        }

        $cabecera = null;
        while ($row = sqlsrv_fetch_object($resultCab)) {
            $cabecera = $row;
            break; // Solo el primer resultado
        }

        if (!$cabecera) {
            return null;
        }

        // Detalle
        $sqlDet = "SET DATEFORMAT YMD;
                   EXEC RO_SP_ECOMMERCE_PEDIDOS_FLUJO_DETALLE '$desde', '$hasta', '$pedidoEsc'";
        $resultDet = sqlsrv_query($this->conn, $sqlDet);

        $detalle = [];
        if ($resultDet !== false) {
            while ($row = sqlsrv_fetch_object($resultDet)) {
                $detalle[] = $row;
            }
        }

        return [
            'cabecera' => $cabecera,
            'detalle'  => $detalle,
        ];
    }

    // -------------------------------------------------------
    // DEVOLUCIONES — CABECERA
    // -------------------------------------------------------

    /**
     * Crea una nueva devolución/cambio y su detalle.
     *
     * @param array $datos   Campos de la cabecera
     * @param array $items   Array de productos [producto_id, producto_nombre, cantidad, accion]
     * @return int|false     ID de la devolución creada, o false en error
     */
    public function crearDevolucion(array $datos, array $items)
    {
        $id = $this->guardarCabecera($datos);
        if ($id === false) return false;
        if (!empty($items)) {
            $this->guardarDetalle($id, $items);
        }
        return $id;
    }

    /**
     * Inserta sólo la cabecera y genera nro_seguimiento.
     * Si $datos['id'] está presente, actualiza en lugar de insertar.
     *
     * @return int|false  ID de la devolución
     */
    public function guardarCabecera(array $datos)
    {
        $fechaPedido = !empty($datos['fecha_pedido'])
            ? $this->parsearFecha($datos['fecha_pedido'])
            : null;

        $precioAbonado   = isset($datos['precio_abonado'])    && $datos['precio_abonado']    !== '' ? (float) $datos['precio_abonado']    : null;
        $precioArtCambio = isset($datos['precio_art_cambio']) && $datos['precio_art_cambio'] !== '' ? (float) $datos['precio_art_cambio'] : null;
        $diferencia      = isset($datos['diferencia_precio']) && $datos['diferencia_precio'] !== '' ? (float) $datos['diferencia_precio'] : null;

        // ---- ACTUALIZAR ----
        if (!empty($datos['id'])) {
            $id = (int) $datos['id'];
            $sql = "UPDATE devoluciones SET
                        tipo             = ?,
                        motivo           = ?,
                        usuario          = ?,
                        observaciones    = ?,
                        nro_rto          = ?,
                        nro_nc_fact      = ?,
                        precio_abonado   = ?,
                        precio_art_cambio= ?,
                        diferencia_precio= ?,
                        link_pago_mp     = ?,
                        nro_operacion_mp = ?
                    WHERE id = ?";
            $params = [
                $datos['tipo'],
                $datos['motivo']         ?? null,
                $datos['usuario']        ?? null,
                $datos['observaciones']  ?? null,
                $datos['nro_rto']        ?? null,
                $datos['nro_nc_fact']    ?? null,
                $precioAbonado,
                $precioArtCambio,
                $diferencia,
                $datos['link_pago_mp']      ?? null,
                $datos['nro_operacion_mp']  ?? null,
                $id,
            ];
            $result = sqlsrv_query($this->conn, $sql, $params);
            return $result !== false ? $id : false;
        }

        // ---- INSERTAR ----
        $sql = "INSERT INTO devoluciones
                    (pedido_id, cliente, fecha_pedido, tipo, motivo, estado, usuario, fecha_creacion,
                     factura, observaciones, nro_rto, nro_nc_fact,
                     precio_abonado, precio_art_cambio, diferencia_precio,
                     link_pago_mp, nro_operacion_mp, nro_ped_tango)
                VALUES (?, ?, ?, ?, ?, 'pendiente', ?, GETDATE(),
                        ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?);
                SELECT SCOPE_IDENTITY() AS nuevo_id;";

        $params = [
            $datos['pedido_id'],
            $datos['cliente']        ?? null,
            $fechaPedido,
            $datos['tipo'],
            $datos['motivo']         ?? null,
            $datos['usuario']        ?? null,
            $datos['factura']        ?? null,
            $datos['observaciones']  ?? null,
            $datos['nro_rto']        ?? null,
            $datos['nro_nc_fact']    ?? null,
            $precioAbonado,
            $precioArtCambio,
            $diferencia,
            $datos['link_pago_mp']      ?? null,
            $datos['nro_operacion_mp']  ?? null,
            $datos['nro_ped_tango']     ?? null,
        ];

        $result = sqlsrv_query($this->conn, $sql, $params);
        if ($result === false) {
            error_log('guardarCabecera INSERT error: ' . print_r(sqlsrv_errors(), true));
            return false;
        }

        sqlsrv_next_result($result);
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        if (!$row || !isset($row['nuevo_id'])) return false;

        $id = (int) $row['nuevo_id'];
        $nroSeg = 'G' . str_pad($id, 15, '0', STR_PAD_LEFT);
        sqlsrv_query($this->conn, "UPDATE devoluciones SET nro_seguimiento = ? WHERE id = ?", [$nroSeg, $id]);

        return $id;
    }

    /**
     * Reemplaza las líneas de detalle de una devolución existente.
     *
     * @param int   $id     ID de la devolución
     * @param array $items  Array de productos
     * @return bool
     */
    public function guardarDetalle(int $id, array $items): bool
    {
        // Eliminar detalle anterior (re-edición)
        $del = sqlsrv_query($this->conn, "DELETE FROM devoluciones_detalle WHERE devolucion_id = ?", [$id]);
        if ($del === false) {
            error_log('guardarDetalle DELETE error: ' . print_r(sqlsrv_errors(), true));
            return false;
        }

        foreach ($items as $item) {
            if (!$this->agregarDetalle($id, $item)) {
                error_log("guardarDetalle: error insertando ítem para devolucion_id=$id");
                return false;
            }
        }
        return true;
    }

    /**
     * Devuelve el nro_seguimiento de un ID.
     */
    public function obtenerNroSeguimiento(int $id): ?string
    {
        $result = sqlsrv_query($this->conn, "SELECT nro_seguimiento FROM devoluciones WHERE id = ?", [$id]);
        if ($result === false) return null;
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        return $row ? (string) $row['nro_seguimiento'] : null;
    }

    /**
     * Inserta una línea de detalle.
     */
    private function agregarDetalle(int $devolucionId, array $item): bool
    {
        $sql = "INSERT INTO devoluciones_detalle
                    (devolucion_id, producto_id, producto_nombre, cantidad, accion,
                     estado_producto, stock_origen, codigo_cambio, descripcion_cambio, stock_cambio)
                VALUES (?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?)";

        $params = [
            $devolucionId,
            $item['producto_id']        ?? null,
            $item['producto_nombre']    ?? null,
            (int) ($item['cantidad']    ?? 1),
            $item['accion'],
            $item['estado_producto']    ?? null,
            $item['stock_origen']       ?? null,
            $item['codigo_cambio']      ?? null,
            $item['descripcion_cambio'] ?? null,
            $item['stock_cambio']       ?? null,
        ];

        $result = sqlsrv_query($this->conn, $sql, $params);
        return $result !== false;
    }

    // -------------------------------------------------------
    // DEVOLUCIONES — LISTADO
    // -------------------------------------------------------

    /**
     * Lista devoluciones con filtros opcionales.
     *
     * @param string|null $estado  Filtrar por estado
     * @param string|null $desde   Fecha desde (Y-m-d)
     * @param string|null $hasta   Fecha hasta (Y-m-d)
     * @param string|null $tipo    Filtrar por tipo ('cambio' | 'devolucion')
     * @return array
     */
    public function listarDevoluciones(
        ?string $estado = null,
        ?string $desde  = null,
        ?string $hasta  = null,
        ?string $tipo   = null
    ): array {
        $where  = [];
        $params = [];

        if (!empty($estado)) {
            $where[]  = 'd.estado = ?';
            $params[] = $estado;
        }
        if (!empty($tipo)) {
            $where[]  = 'd.tipo = ?';
            $params[] = $tipo;
        }
        if (!empty($desde)) {
            $where[]  = 'CONVERT(date, d.fecha_creacion) >= ?';
            $params[] = $desde;
        }
        if (!empty($hasta)) {
            $where[]  = 'CONVERT(date, d.fecha_creacion) <= ?';
            $params[] = $hasta;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT
                    d.id,
                    d.nro_seguimiento,
                    d.pedido_id,
                    d.cliente,
                    d.tipo,
                    d.motivo,
                    d.estado,
                    d.usuario,
                    d.factura,
                    d.observaciones,
                    d.nro_rto,
                    d.nro_nc_fact,
                    d.precio_abonado,
                    d.precio_art_cambio,
                    d.diferencia_precio,
                    d.link_pago_mp,
                    d.nro_operacion_mp,
                    d.nro_ped_tango,
                    CONVERT(varchar(19), d.fecha_creacion, 120)   AS fecha_creacion,
                    CONVERT(varchar(19), d.fecha_pedido, 120)     AS fecha_pedido,
                    CONVERT(varchar(19), d.fecha_resolucion, 120) AS fecha_resolucion,
                    (SELECT COUNT(*) FROM devoluciones_detalle dd WHERE dd.devolucion_id = d.id) AS total_items
                FROM devoluciones d
                $whereClause
                ORDER BY d.fecha_creacion DESC";

        $result = sqlsrv_query($this->conn, $sql, $params ?: null);

        if ($result === false) {
            error_log('listarDevoluciones error: ' . print_r(sqlsrv_errors(), true));
            return [];
        }

        $data = [];
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    // -------------------------------------------------------
    // DEVOLUCIONES — DETALLE INDIVIDUAL
    // -------------------------------------------------------

    /**
     * Trae cabecera + detalle de una devolución.
     *
     * @param int $id
     * @return array|null
     */
    public function obtenerDevolucion(int $id): ?array
    {
        // Cabecera
        $sqlCab = "SELECT
                       d.id, d.nro_seguimiento, d.pedido_id, d.cliente, d.tipo, d.motivo, d.estado, d.usuario,
                       d.factura, d.observaciones, d.nro_rto, d.nro_nc_fact,
                       d.precio_abonado, d.precio_art_cambio, d.diferencia_precio,
                       d.link_pago_mp, d.nro_operacion_mp, d.nro_ped_tango,
                       CONVERT(varchar(19), d.fecha_creacion, 120)   AS fecha_creacion,
                       CONVERT(varchar(19), d.fecha_pedido, 120)     AS fecha_pedido,
                       CONVERT(varchar(19), d.fecha_resolucion, 120) AS fecha_resolucion
                   FROM devoluciones d
                   WHERE d.id = ?";

        $resultCab = sqlsrv_query($this->conn, $sqlCab, [$id]);

        if ($resultCab === false) {
            return null;
        }

        $cabecera = sqlsrv_fetch_array($resultCab, SQLSRV_FETCH_ASSOC);
        if (!$cabecera) {
            return null;
        }

        // Líneas de detalle
        $sqlDet = "SELECT id, producto_id, producto_nombre, cantidad, accion,
                          estado_producto, stock_origen, codigo_cambio, descripcion_cambio, stock_cambio
                   FROM devoluciones_detalle
                   WHERE devolucion_id = ?
                   ORDER BY id";

        $resultDet = sqlsrv_query($this->conn, $sqlDet, [$id]);
        $detalle   = [];

        if ($resultDet !== false) {
            while ($row = sqlsrv_fetch_array($resultDet, SQLSRV_FETCH_ASSOC)) {
                $detalle[] = $row;
            }
        }

        return ['cabecera' => $cabecera, 'detalle' => $detalle];
    }

    // -------------------------------------------------------
    // DEVOLUCIONES — ACTUALIZAR ESTADO
    // -------------------------------------------------------

    /**
     * Actualiza el estado de una devolución.
     * Si el nuevo estado es 'resuelto', setea fecha_resolucion = GETDATE().
     *
     * @param int    $id
     * @param string $estado
     * @return bool
     */
    public function actualizarEstado(int $id, string $estado): bool
    {
        $estadosValidos = ['pendiente', 'en_transito', 'recibido', 'resuelto'];
        if (!in_array($estado, $estadosValidos, true)) {
            return false;
        }

        if ($estado === 'resuelto') {
            $sql = "UPDATE devoluciones
                    SET estado = ?, fecha_resolucion = GETDATE()
                    WHERE id = ?";
        } else {
            $sql = "UPDATE devoluciones
                    SET estado = ?, fecha_resolucion = NULL
                    WHERE id = ?";
        }

        $result = sqlsrv_query($this->conn, $sql, [$estado, $id]);

        if ($result === false) {
            error_log('actualizarEstado error: ' . print_r(sqlsrv_errors(), true));
            return false;
        }

        return sqlsrv_rows_affected($result) > 0;
    }

    // -------------------------------------------------------
    // ARTÍCULOS — BÚSQUEDA POR CÓDIGO
    // -------------------------------------------------------

    /**
     * Busca un artículo por código exacto en STA11.
     *
     * @param string $codigo
     * @return array|null  ['codigo' => ..., 'descripcion' => ...]
     */
    public function buscarArticulo(string $codigo): ?array
    {
        $codigoEsc = str_replace("'", "''", trim($codigo));

        $sql = "SELECT TOP 1
                    COD_ARTICU  AS codigo,
                    DESCRIPCIO  AS descripcion
                FROM STA11
                WHERE COD_ARTICU = '$codigoEsc' COLLATE DATABASE_DEFAULT";

        $result = sqlsrv_query($this->conn, $sql);

        if ($result === false) {
            error_log('buscarArticulo error: ' . print_r(sqlsrv_errors(), true));
            return null;
        }

        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return [
            'codigo'      => trim((string)$row['codigo']),
            'descripcion' => trim((string)$row['descripcion']),
        ];
    }

    // -------------------------------------------------------
    // LOCALES / TIENDAS
    // -------------------------------------------------------

    /**
     * Lista todas las tiendas/depósitos disponibles para Select2.
     *
     * @return array  [['id' => string, 'text' => string], ...]
     */
    public function listarLocales(): array
    {
        $sql = "SELECT DISTINCT
                    COD_DEPOSI_ECOMM AS id,
                    SUCURSAL         AS texto
                FROM RO_T_DEPOSITOS_ECOMMERCE_TIENDAS
                WHERE SUCURSAL IS NOT NULL AND SUCURSAL <> ''
                ORDER BY SUCURSAL";

        $result  = sqlsrv_query($this->conn, $sql);
        $locales = [];

        if ($result !== false) {
            while ($row = sqlsrv_fetch_object($result)) {
                $locales[] = [
                    'id'   => trim((string)($row->id   ?? '')),
                    'text' => trim((string)($row->texto ?? '')),
                ];
            }
        }

        return $locales;
    }

    // -------------------------------------------------------
    // UTILIDADES PRIVADAS
    // -------------------------------------------------------

    /**
     * Parsea texto de fecha al formato 'Y-m-d H:i:s' que entiende sqlsrv.
     */
    private function parsearFecha(string $texto): ?string
    {
        $fecha = date_create($texto);
        return $fecha ? date_format($fecha, 'Y-m-d H:i:s') : null;
    }
}
