<?php
	ob_end_clean();
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('memory_limit','512M');
	set_time_limit(95);

	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');
	
	require_once '../funciones_globales/PHPExcel179/PHPExcel.php';
	
	$objPHPExcel	= new PHPExcel();
	$pag_IDE		= request_var( 'pag_IDE', 0 );//id_empresa
	
	if( !$pag_IDE )
		$empresa_desc = "TODOS";
	else
		$empresa_desc = numbre_sucursal( $pag_IDE );
	
	$datos			= lista_articulos_excel();
	
	$fecha_mov		= date( 'd-m-Y H:i:s' );
	
	//configuracion general
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth( 3 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth( 5 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 20 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth( 60 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth( 10 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth( 10 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth( 15 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth( 15 );
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth( 15 );
	
	$objPHPExcel->getActiveSheet()->getStyle('D2:D4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B6:I6')->getFont()->setBold(true);
	
	$objPHPExcel->getActiveSheet()->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle("E6:I6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	//HOJA 1
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'C2', 'REPORTE' );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'C3', 'SUCURSAL' );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'C4', 'FECHA' );
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'D2', 'LISTADO DE ARTÍCULOS' );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'D3', "$empresa_desc" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'D4', "$fecha_mov" );
	
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "B6", "#" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "C6", "CODIGO" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "D6", "DESCRIPCION" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "E6", "STOCK" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "F6", "COSTO" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "G6", "PRECIO" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "H6", "MAYOREO 1" );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "I6", "MAYOREO 2" );
	
	//area de datos de la hoja 1
	$fila	= 7;
	$i		= 1;
	foreach( $datos as $detalle )
	{
		$objPHPExcel->getActiveSheet()->getStyle( "E$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00 );
		$objPHPExcel->getActiveSheet()->getStyle( "F$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
		$objPHPExcel->getActiveSheet()->getStyle( "G$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
		$objPHPExcel->getActiveSheet()->getStyle( "H$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
		$objPHPExcel->getActiveSheet()->getStyle( "I$fila" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "B$fila", $i );
		$objPHPExcel->getActiveSheet()->getCell( "C$fila" )->setValueExplicit( $detalle['codigo'], PHPExcel_Cell_DataType::TYPE_STRING );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "D$fila", $detalle['descripcion'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "E$fila", $detalle['existencia'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "F$fila", $detalle['costo'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "G$fila", $detalle['precio'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "H$fila", $detalle['mayoreo_1'] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( "I$fila", $detalle['mayoreo_2'] );
		
		$fila++;
		$i++;
	}
	
	//FINAL
	$objPHPExcel->getActiveSheet()->setTitle('Resultado');
	$objPHPExcel->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header("Content-Disposition: attachment;filename=LISTA_ARTÍCULOS_$fecha_mov.xlsx");
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>