
<!-- modals.php -->

<!-- Todos los modales -->
<?php ob_start(); try { include 'modal-nc-promociones.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-historial-nc-promociones.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-nc-devoluciones.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-ordenes-sin-integrar.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-remitos-sin-integrar.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-flex-pendientes.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-pendientes-preparar.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-facturas-sin-remito.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-ordenes-pendiente-cierre.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-pendiente-despacho.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-productos-ml-full.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-despachados.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-pendiente-control.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-ranking-pedidos-control.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-recibidos-no-entregados.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-retiro-tienda.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<!-- Nuevos modales para control separado -->
<?php ob_start(); try { include 'modal-pedidos-pendiente-control-central.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-pendiente-control-sucursales.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-ranking-pedidos-control-sucursales.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<!-- Modales para pedidos incompletos -->
<?php ob_start(); try { include 'modal-pedidos-incompletos-central.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-incompletos-sucursales.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<!-- Modal para Uruguay -->
<?php ob_start(); try { include 'modal-pedidos-sin-facturar-uruguay.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-sin-remito-uruguay.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-nc-devoluciones-uruguay.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-ordenes-sin-integrar-uruguay.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-ordenes-pendiente-cierre-uruguay.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-retiro-tienda-uruguay.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>
<?php ob_start(); try { include 'modal-pedidos-pendiente-control-sucursales-uruguay.php'; ob_end_flush(); } catch (Throwable $e) { ob_end_clean(); } ?>