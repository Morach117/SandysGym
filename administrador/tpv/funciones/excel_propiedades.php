<?php	
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("LSC. José Isaac Vázquez Velázquez")
								 ->setLastModifiedBy("LSC. José Isaac Vázquez Velázquez")
								 ->setTitle("Seguimiento de Egresados")
								 ->setSubject("Seguimiento de Egresados - UNACH")
								 ->setDescription("Seguimiento de Egresados de la DES Ciencias Administrativas y Contables - UNACH.")
								 ->setKeywords("LSC. José Isaac Vázquez Velázquez")
								 ->setCategory("Datos Estadisticos");
	
	$styleThinBlackBorderOutline = array(
										'borders' => array(
											'outline' => array(
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												'color' => array('argb' => 'FF000000'),
											),
										),
									);
	
	$BordeTodos = array(
										'borders' => array(
											'allborders' => array(
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												'color' => array('argb' => 'FF000000'),
											),
										),
									);
?>