<?php
	ob_end_clean();
	require_once( "/homepages/11/d586392221/htdocs/funciones_globales/TCPDF8/tcpdf.php" );
	
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_comunes.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	require_once( "funciones/sesiones.php" );

	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$folio		= request_var( 'folio', 0 );
	$id_venta	= request_var( 'id', 0 );
	$tipo		= request_var( 'tipo', '' );
	
	if( $tipo == 'PEDREDONES' )
		$tipo = "EDREDONES";
	
	if( $tipo == 'LAVANDERIA'  )
		$tag = "KGS.";

	if (  $tipo == 'EDREDONES')
		$tag = "PZS.";
		
	//no utilizare el anio ni socio
	
	if( $folio && $id_venta )
	{
		$empresa		= detalle_empresa();
		$detalle_ticket	= detalle_ticket( $folio, $id_venta );
		$venta_ticket	= venta_ticket( $folio, $id_venta );
		
		if( $detalle_ticket && $venta_ticket )
		{
			$folio			= str_pad( $folio, 7, '0', STR_PAD_LEFT );
		
			ticket( $folio, $detalle_ticket, $venta_ticket, $empresa, $tag, $tipo );
			
		}
		else
			getPDF_error("No se encontraron registros." );
	}
	else
		getPDF_error("Datos inválidos." );
	
	function ticket( $folio, $dticket, $vticket, $empresa, $tag, $tipo )
	{
	    //die( dirname(__FILE__) );
		$pdf = new TCPDF( 'L', 'mm', 'LETTER', true, 'UTF-8', false );
		$pdf -> SetCreator( 'iSac Vázquez' );
		$pdf -> SetTitle( 'Ticket' );
		$pdf -> SetAutoPageBreak( true, 10 );
		$pdf -> SetMargins( 10, 10, 10 );
		$pdf -> SetPrintHeader( false );
		$pdf -> SetPrintFooter( false );
		$pdf -> AddPage();
		$pdf -> SetFont( 'helvetica', '', 10 );
		
		$pdf -> SetFillColor( 66, 139, 202 );
		//cuadros grandes
		$pdf -> Rect( 10, 57, 124, 145 );//x,y aancho,alto
		$pdf -> Rect( 145, 57, 124, 145 );//x,y aancho,alto
		
		//cuadros medios del ancabezado de la nota
		$pdf -> Rect( 10, 33, 124, 25 );//x,y aancho,alto
		$pdf -> Rect( 145, 33, 124, 25 );//x,y aancho,alto
	
		if( file_exists( "../imagenes/empresa_$empresa[id_empresa].png" ) )
		{
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].png", 10, 10, 35 ); // "ruta", x, y, w
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].png", 145, 10, 35 ); // "ruta", x, y, w
			//marca de agua
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].png", 36, 120, 80); // "ruta", x, y, w
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].png", 179, 120, 80); // "ruta", x, y, w
		}
		elseif( file_exists( "../imagenes/empresa_$empresa[id_empresa].jpg" ) )
		{
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].jpg", 10, 10, 35 ); // "ruta", x, y, w
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].jpg", 145, 10, 35 ); // "ruta", x, y, w
			//marca de agua
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].jpg", 36, 120, 80); // "ruta", x, y, w
			$pdf ->Image("../imagenes/empresa_$empresa[id_empresa].jpg", 179, 120, 80); // "ruta", x, y, w
		}
		
		//titulos de la nota, APARTIR DE ACA, ToDo ESTA AGRUPADO POR FILA
		
		$alto		= 5;
		$v_cantidad	= 0;
		$subtotal	= 0;
		$descuento	= "";
		
		$pdf -> Cell( 30, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, $empresa['direccion'], 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, $tipo, 1, 0, 'C', false );
		$pdf -> Cell( 41, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, $empresa['direccion'], 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, $tipo, 1, 1, 'C', false );
		
		$pdf -> Cell( 30, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, $empresa['colonia'], 0, 0, 'C' );
		$pdf -> SetFont( 'Helvetica', 'B', 16 );
		$pdf -> Cell( 30, $alto, "$folio", 0, 0, 'C' );
		$pdf -> SetFont( 'Arial', '', 8 );
		$pdf -> Cell( 41, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, $empresa['colonia'], 0, 0, 'C' );
		$pdf -> SetFont( 'Helvetica', 'B', 16 );
		$pdf -> Cell( 30, $alto, "$folio", 0, 1, 'C' );
		$pdf -> SetFont( 'Arial', '', 8 );
		
		$pdf -> Cell( 30, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, $empresa['ciudad'], 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, "CLIENTE", 1, 0, 'C', true );
		$pdf -> Cell( 41, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, $empresa['ciudad'], 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, "CLIENTE", 1, 1, 'C', true );
		
		$pdf -> Cell( 30, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, "TEL.: ".$empresa['telefono'], 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, "No. ".$vticket['id_socio'], 1, 0, 'C' );
		$pdf -> Cell( 41, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 64, $alto, "TEL.: ".$empresa['telefono'], 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, "No. ".$vticket['id_socio'], 1, 0, 'C' );
		
		//encabezados de la venta
		
		$pdf -> Ln( $alto );
		$alto = 6;
		
		$pdf -> Cell( 20, $alto, "NOMBRE", 0, 0, 'L' );
		$pdf -> Cell( 104, $alto, $vticket['nombres'], 'B', 0, 'L' );
		$pdf -> Cell( 11, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 20, $alto, "NOMBRE", 0, 0, 'L' );
		$pdf -> Cell( 104, $alto, $vticket['nombres'], 'B', 1, 'L' );
		
		$pdf -> Cell( 20, $alto, "TELEFONO", 0, 0, 'L' );
		$pdf -> Cell( 104, $alto, $vticket['telfijo']." | ".$vticket['telcel'], 'B', 0, 'L' );
		$pdf -> Cell( 11, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 20, $alto, "TELEFONO", 0, 0, 'L' );
		$pdf -> Cell( 104, $alto, $vticket['telfijo']." | ".$vticket['telcel'], 'B', 1, 'L' );
		
		$pdf -> Cell( 30, $alto, "FECHA RECEPCION", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['f_recepcion'], 'B', 0, 'L' );
		$pdf -> Cell( 30, $alto, "FECHA ENTREGA", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['f_entrega'], 'B', 0, 'L' );
		$pdf -> Cell( 11, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, "FECHA RECEPCION", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['f_recepcion'], 'B', 0, 'L' );
		$pdf -> Cell( 30, $alto, "FECHA ENTREGA", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['f_entrega'], 'B', 1, 'L' );
		
		$pdf -> Cell( 30, $alto, "HORA RECEPCION", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['h_recepcion'], 'B', 0, 'L' );
		$pdf -> Cell( 30, $alto, "HORA ENTREGA", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['h_entrega'], 'B', 0, 'L' );
		$pdf -> Cell( 11, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 30, $alto, "HORA RECEPCION", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['h_recepcion'], 'B', 0, 'L' );
		$pdf -> Cell( 30, $alto, "HORA ENTREGA", 0, 0, 'L' );
		$pdf -> Cell( 32, $alto, $vticket['h_entrega'], 'B', 0, 'L' );
		
		$pdf -> Ln( 7 );
		
		$pdf -> Cell( 12, $alto, "$tag", 1, 0, 'C', true );
		$pdf -> Cell( 72, $alto, "DESCRIPCION", 1, 0, 'C', true );
		$pdf -> Cell( 20, $alto, "PRECIO", 1, 0, 'R', true );
		$pdf -> Cell( 20, $alto, "IMPORTE", 1, 0, 'R', true );
		$pdf -> Cell( 11, $alto, "", 0, 0, 'C' );
		$pdf -> Cell( 12, $alto, "$tag", 1, 0, 'C', true );
		$pdf -> Cell( 72, $alto, "DESCRIPCION", 1, 0, 'C', true );
		$pdf -> Cell( 20, $alto, "PRECIO", 1, 0, 'R', true );
		$pdf -> Cell( 20, $alto, "IMPORTE", 1, 1, 'R', true );
		
		//detalle de la venta
		
		foreach( $dticket as $detalle )
		{
			$pdf -> Cell( 12, $alto, $detalle['kilo'], 0, 0, 'C' );
			$pdf -> Cell( 72, $alto, $detalle['descripcion'], 0, 0, 'L' );
			$pdf -> Cell( 20, $alto, '$'.$detalle['precio'], 0, 0, 'R' );
			$pdf -> Cell( 20, $alto, '$'.$detalle['importe'], 0, 0, 'R' );
			
			$pdf -> Cell( 11, $alto, "", 0, 0, 'C' );
			
			$pdf -> Cell( 12, $alto, $detalle['kilo'], 0, 0, 'C' );
			$pdf -> Cell( 72, $alto, $detalle['descripcion'], 0, 0, 'L' );
			$pdf -> Cell( 20, $alto, '$'.$detalle['precio'], 0, 0, 'R' );
			$pdf -> Cell( 20, $alto, '$'.$detalle['importe'], 0, 1, 'R' );
			
			$v_cantidad	+= $detalle['kilo'];
			$subtotal	+= $detalle['importe'];
		}
		
		//area del total
		
		$pdf -> SetY( 160 );
		$alto = 5;
		
		if( $subtotal > $vticket['total'] )
		{
			$descuento	= $subtotal - $vticket['total'];
			$descuento	= "DESCUENTO: $". number_format( $descuento, 2 ).". ";
		}
		
		$pdf -> Cell( 124, $alto, 'OBSERVACIONES', 1, 0, 'C', true );
		$pdf -> Cell( 11, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 124, $alto, 'OBSERVACIONES', 1, 1, 'C', true );
		
		$pdf -> SetFont( 'Arial', '', 12 );
		$pdf -> MultiCell( 124, $alto, number_format( $v_cantidad, 2 )." $tag S:_____ L:_____ $descuento".$vticket['obs'], 1, 'L' );//138 caracteres como maximo
		$pdf -> SetXY( $pdf -> GetX() + 135, 165 );
		$pdf -> MultiCell( 124, $alto, number_format( $v_cantidad, 2 )." $tag S:_____ L:_____ $descuento".$vticket['obs'], 1, 'L' );
		
		$pdf -> SetY( 182 );
		$pdf -> SetFont( 'Arial', '', 10 );
		
		$tag_sub_iva	= "SUBTOTAL";
		$mon_sub_iva	= number_format( $subtotal, 2 );
		$tag_pago		= "";
		$tag_pago_total	= 0;
		
		if( $vticket['iva_monto'] )
		{
			$tag_sub_iva = "SUB. + IVA.";
			$mon_sub_iva = $mon_sub_iva." + $".number_format( $vticket['iva_monto'], 2 );
		}
		
		if( $vticket['tipo_pago'] == 'E' || $vticket['tipo_pago'] == 'C' )
		{
			$tag_pago		= "ANTICIPO";
			$tag_pago_total	= $vticket['efectivo'];
		}
		else
		{
			$tag_pago		= "TARJETA";
			$tag_pago_total	= $vticket['tar_com'];
		}
		
		$pdf -> Cell( 69, $alto, 'FIRMA DEL CLIENTE', 'T', 0, 'C' );
		$pdf -> Cell( 25, $alto, $tag_sub_iva, 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$mon_sub_iva, 1, 0, 'R' );
		$pdf -> Cell( 11, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 69, $alto, 'FIRMA DEL CLIENTE', 'T', 0, 'C' );
		$pdf -> Cell( 25, $alto, $tag_sub_iva, 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$mon_sub_iva, 1, 1, 'R' );
		
		$pdf -> Cell( 69, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 25, $alto, "TOTAL", 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$vticket['total'], 1, 0, 'R' );
		$pdf -> Cell( 80, $alto, '', 0, 0, 'C' );
		$pdf -> Cell( 25, $alto, "TOTAL", 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$vticket['total'], 1, 1, 'R' );
		
		$pdf -> Cell( 69, $alto, "", 0, 0, 'C' );
		$pdf -> Cell( 25, $alto, $tag_pago, 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$tag_pago_total, 1, 0, 'R' );
		$pdf -> Cell( 80, $alto, "", 0, 0, 'C' );
		$pdf -> Cell( 25, $alto, $tag_pago, 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$tag_pago_total, 1, 1, 'R' );
		
		// $pdf -> SetFont( 'Helvetica', 'B', 16 );
		// $pdf -> Cell( 69, $alto, "FOLIO No. $folio", 0, 0, 'C', false );
		$pdf -> Cell( 69, $alto, "", 0, 0, 'C', false );
		$pdf -> SetFillColor( 66, 139, 202 );
		$pdf -> SetFont( 'Arial', 'B', 11 );
		$pdf -> Cell( 25, $alto, "POR PAGAR", 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$vticket['credito'], 1, 0, 'R' );
		$pdf -> Cell( 11, $alto, '', 0, 0, 'C' );
		// $pdf -> SetFont( 'Helvetica', 'B', 16 );
		// $pdf -> Cell( 69, $alto, "FOLIO No. $folio", 0, 0, 'C', false );
		$pdf -> Cell( 69, $alto, "", 0, 0, 'C', false );
		$pdf -> SetFillColor( 66, 139, 202 );
		$pdf -> SetFont( 'Arial', 'B', 11 );
		$pdf -> Cell( 25, $alto, "POR PAGAR", 1, 0, 'L', true );
		$pdf -> Cell( 30, $alto, '$'.$vticket['credito'], 1, 1, 'R' );
		
		$pdf -> SetY( 182 );
		$pdf -> SetFont( 'Arial', 'B', 50 );
		
		$pdf -> Cell( 69, $alto, (int)$folio, 0, 0, 'C', false );
		
		$pdf -> SetX( 145 );
		$pdf -> Cell( 69, $alto, (int)$folio, 0, 0, 'C', false );
		
		$pdf -> Output( "$folio.pdf", 'I' );		//D
	}
	
?>