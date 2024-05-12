<?php
	ob_end_clean();
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('memory_limit','512M');
	set_time_limit(95);

	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');
	
	require_once '../../funciones_globales/PHPExcel-1.8/PHPExcel.php';
	
	$objPHPExcel	= new PHPExcel();
	
	$año			= request_var( 'anio', date( 'Y' ) );
	$busqueda		= request_var( 'busqueda', '' );
	$mes_evaluar	= request_var( 'mes_evaluar', '' );
	$cliente		= request_var( 'nombre_cliente', '' );
	$fecha_mov		= date( 'd-m-Y H:i:s' );
	
	$datos			= obtene_info_bd( $busqueda, $año, $mes_evaluar, $cliente );
	
	//configuracion general
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth( 3 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth( 5 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 10 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth( 20 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth( 10 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth( 10 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth( 10 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth( 25 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth( 25 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth( 20 );
	
	$objPHPExcel->getActiveSheet()->getStyle('D2:D4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6:I6')->getFont()->setBold(true);
	
	$objPHPExcel->getActiveSheet()->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle("E6:G6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	//HOJA 1
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'C2', 'REPORTE' );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'C3', 'SUCURSAL' );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'C4', 'FECHA' );
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'D2', 'LISTADO DE TICKETS' );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'D3', "$empresa_desc" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'D4', "$fecha_mov" );
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "B6", "#" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "C6", "FOLIO" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "D6", "PRIMER MOVIMIENTO" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "E6", "EFECTIVO" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "F6", "POR COBRAR" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "G6", "TOTAL" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "H6", "CLIENTE" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "I6", "USUARIO" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "J6", "STATUS" );
	
	// area de datos de la hoja 1
	$fila	= 7;
	$i		= 1;
	foreach( $datos as $detalle )
	{
		$objPHPExcel->getActiveSheet()->getStyle( "E$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00 );
		$objPHPExcel->getActiveSheet()->getStyle( "F$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
		$objPHPExcel->getActiveSheet()->getStyle( "G$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "B$fila", $i );
		$objPHPExcel->getActiveSheet()->getCell( "C$fila" )->setValueExplicit( $detalle['folio_desc'], PHPExcel_Cell_DataType::TYPE_STRING );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "D$fila", $detalle['primer_m'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "E$fila", $detalle['efectivo'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "F$fila", $detalle['credito'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "G$fila", $detalle['total'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "H$fila", $detalle['cliente'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "I$fila", $detalle['usuario'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "J$fila", $detalle['status_desc'] );
		
		$fila++;
		$i++;
	}
	
	//FINAL
	$objPHPExcel->getActiveSheet()->setTitle('Resultado');
	$objPHPExcel->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header("Content-Disposition: attachment;filename=Tickets_$fecha_mov.xlsx");
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>