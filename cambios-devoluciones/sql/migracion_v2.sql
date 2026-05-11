-- ============================================================
-- Script: Migración v2 — Nuevos campos de Logística Inversa
-- Ejecutar sobre la base de datos central
-- Idempotente: verifica existencia de cada columna antes de agregarla
-- ============================================================

-- -------------------------------------------------------
-- Columnas nuevas en: devoluciones
-- -------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'nro_seguimiento')
    ALTER TABLE devoluciones ADD nro_seguimiento VARCHAR(20) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'factura')
    ALTER TABLE devoluciones ADD factura VARCHAR(50) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'observaciones')
    ALTER TABLE devoluciones ADD observaciones VARCHAR(500) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'nro_rto')
    ALTER TABLE devoluciones ADD nro_rto VARCHAR(50) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'nro_nc_fact')
    ALTER TABLE devoluciones ADD nro_nc_fact VARCHAR(50) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'precio_abonado')
    ALTER TABLE devoluciones ADD precio_abonado DECIMAL(12,2) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'precio_art_cambio')
    ALTER TABLE devoluciones ADD precio_art_cambio DECIMAL(12,2) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'diferencia_precio')
    ALTER TABLE devoluciones ADD diferencia_precio DECIMAL(12,2) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'link_pago_mp')
    ALTER TABLE devoluciones ADD link_pago_mp VARCHAR(500) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'nro_operacion_mp')
    ALTER TABLE devoluciones ADD nro_operacion_mp VARCHAR(100) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones') AND name = 'nro_ped_tango')
    ALTER TABLE devoluciones ADD nro_ped_tango VARCHAR(50) NULL;
GO

-- Generar nro_seguimiento para registros existentes que no lo tengan
UPDATE devoluciones
SET nro_seguimiento = 'G' + RIGHT('000000000000' + CAST(id AS VARCHAR(12)), 12)
WHERE nro_seguimiento IS NULL;
GO

-- -------------------------------------------------------
-- Columnas nuevas en: devoluciones_detalle
-- -------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones_detalle') AND name = 'estado_producto')
    ALTER TABLE devoluciones_detalle ADD estado_producto VARCHAR(20) NULL; -- 'nuevo', 'usado', 'fallado'
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones_detalle') AND name = 'stock_origen')
    ALTER TABLE devoluciones_detalle ADD stock_origen VARCHAR(100) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones_detalle') AND name = 'codigo_cambio')
    ALTER TABLE devoluciones_detalle ADD codigo_cambio VARCHAR(50) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones_detalle') AND name = 'descripcion_cambio')
    ALTER TABLE devoluciones_detalle ADD descripcion_cambio VARCHAR(200) NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('devoluciones_detalle') AND name = 'stock_cambio')
    ALTER TABLE devoluciones_detalle ADD stock_cambio VARCHAR(100) NULL;
GO

-- Índice útil para búsqueda por nro_seguimiento
IF NOT EXISTS (
    SELECT 1 FROM sys.indexes
    WHERE object_id = OBJECT_ID('devoluciones') AND name = 'IX_devoluciones_nro_seguimiento'
)
BEGIN
    CREATE INDEX IX_devoluciones_nro_seguimiento ON devoluciones (nro_seguimiento);
    PRINT 'Índice IX_devoluciones_nro_seguimiento creado.';
END
GO

PRINT 'Migración v2 finalizada correctamente.';
GO
