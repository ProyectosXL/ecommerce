<?php
define("DB_SERVER", "SERVIDOR");
define("DB_USERNAME", "sa");
define("DB_PASSWORD", "Axoft1988");
define("DB_PORT", "1433");
define("DB_DATABASE", "LAKER_SA");
function new_ml()
{   
	require_once 'Class/Conexion.php';
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

	try {

		$sql = "SELECT * FROM RO_VIEW_INSERT_ML_SOF_AUDITORIA";
		$stmt = sqlsrv_query($cid_central, $sql);
		
		while( $v = sqlsrv_fetch_array( $stmt) ) {
			$sql2 = "SET NOCOUNT ON; EXEC RO_SP_INSERT_ML_SOF_AUDITORIA";
			sqlsrv_query($cid_central, $sql2);
		}

	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}
