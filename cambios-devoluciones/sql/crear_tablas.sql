-- ============================================================
-- Script: Creación de tablas para módulo Logística Inversa
-- Base de datos: DATABASE_CENTRAL (misma que usa el sistema)
-- Ejecutar conectado a la base de datos correcta
-- ============================================================

-- -------------------------------------------------------
-- Tabla: devoluciones
-- Cabecera de cada devolución o cambio de pedido
-- -------------------------------------------------------
IF NOT EXISTS (
    SELECT 1 FROM sys.objects WHERE name = 'devoluciones' AND type = 'U'
)
BEGIN
    CREATE TABLE devoluciones (
        id                INT           IDENTITY(1,1)   NOT NULL,
        pedido_id         VARCHAR(100)  NOT NULL,
        cliente           VARCHAR(150)  NULL,
        fecha_pedido      DATETIME      NULL,
        tipo              VARCHAR(20)   NOT NULL
                              CONSTRAINT CK_devoluciones_tipo
                              CHECK (tipo IN ('cambio', 'devolucion')),
        motivo            VARCHAR(100)  NULL,
        estado            VARCHAR(50)   NOT NULL DEFAULT 'pendiente'
                              CONSTRAINT CK_devoluciones_estado
                              CHECK (estado IN ('pendiente', 'en_transito', 'recibido', 'resuelto')),
        usuario           VARCHAR(100)  NULL,
        fecha_creacion    DATETIME      NOT NULL DEFAULT GETDATE(),
        fecha_resolucion  DATETIME      NULL,

        nro_seguimiento    VARCHAR(20)   NULL,
        nro_rto            VARCHAR(50)   NULL,
        nro_nc_fact        VARCHAR(50)   NULL,
        factura            VARCHAR(50)   NULL,
        observaciones      VARCHAR(500)  NULL,
        precio_abonado     DECIMAL(12,2) NULL,
        precio_art_cambio  DECIMAL(12,2) NULL,
        diferencia_precio  DECIMAL(12,2) NULL,
        link_pago_mp       VARCHAR(500)  NULL,
        nro_operacion_mp   VARCHAR(100)  NULL,
        nro_ped_tango      VARCHAR(50)   NULL,

        CONSTRAINT PK_devoluciones PRIMARY KEY (id)
    );

    PRINT 'Tabla devoluciones creada correctamente.';
END
ELSE
BEGIN
    PRINT 'La tabla devoluciones ya existe. No se realizaron cambios.';
END
GO

-- -------------------------------------------------------
-- Tabla: devoluciones_detalle
-- Líneas de productos dentro de cada devolución/cambio
-- -------------------------------------------------------
IF NOT EXISTS (
    SELECT 1 FROM sys.objects WHERE name = 'devoluciones_detalle' AND type = 'U'
)
BEGIN
    CREATE TABLE devoluciones_detalle (
        id               INT           IDENTITY(1,1)   NOT NULL,
        devolucion_id    INT           NOT NULL,
        producto_id      VARCHAR(50)   NULL,
        producto_nombre  VARCHAR(200)  NULL,
        cantidad         INT           NOT NULL DEFAULT 1,
        accion           VARCHAR(20)   NOT NULL
                             CONSTRAINT CK_devoluciones_detalle_accion
                             CHECK (accion IN ('cambio', 'devolucion')),
        estado_producto    VARCHAR(20)   NULL
                             CONSTRAINT CK_devoluciones_detalle_estado_prod
                             CHECK (estado_producto IS NULL OR estado_producto IN ('nuevo', 'usado', 'fallado')),
        stock_origen       VARCHAR(100)  NULL,
        codigo_cambio      VARCHAR(50)   NULL,
        descripcion_cambio VARCHAR(200)  NULL,
        stock_cambio       VARCHAR(100)  NULL,

        CONSTRAINT PK_devoluciones_detalle PRIMARY KEY (id),

        CONSTRAINT FK_devoluciones_detalle_devolucion
            FOREIGN KEY (devolucion_id) REFERENCES devoluciones(id)
            ON DELETE CASCADE
    );

    -- Índice para acelerar búsquedas por devolucion_id
    CREATE INDEX IX_devoluciones_detalle_devolucion_id
        ON devoluciones_detalle (devolucion_id);

    PRINT 'Tabla devoluciones_detalle creada correctamente.';
END
ELSE
BEGIN
    PRINT 'La tabla devoluciones_detalle ya existe. No se realizaron cambios.';
END
GO

-- -------------------------------------------------------
-- Índices adicionales sobre devoluciones
-- -------------------------------------------------------
IF NOT EXISTS (
    SELECT 1 FROM sys.indexes
    WHERE object_id = OBJECT_ID('devoluciones') AND name = 'IX_devoluciones_pedido_id'
)
BEGIN
    CREATE INDEX IX_devoluciones_pedido_id ON devoluciones (pedido_id);
    PRINT 'Índice IX_devoluciones_pedido_id creado.';
END
GO

IF NOT EXISTS (
    SELECT 1 FROM sys.indexes
    WHERE object_id = OBJECT_ID('devoluciones') AND name = 'IX_devoluciones_estado'
)
BEGIN
    CREATE INDEX IX_devoluciones_estado ON devoluciones (estado);
    PRINT 'Índice IX_devoluciones_estado creado.';
END
GO

PRINT 'Script finalizado correctamente.';
GO
