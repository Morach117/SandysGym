<?php
	function obtener_p_comision_tarjeta()
	{
		Global $conexion, $id_consorcio;
		
		$query		= "	SELECT	con_comision_tarjeta
						FROM 	san_consorcios
						WHERE	con_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['con_comision_tarjeta'];
		
		return 0;
	}
	
	function obtener_datos_cliente( $id_cliente )
	{
		Global $conexion, $id_consorcio;
		
		$query		= "	SELECT		* 
						FROM 		san_socios 
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = soc_id_empresa
						AND 		coem_id_consorcio = $id_consorcio
						WHERE 		soc_id_socio = $id_cliente";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		
		return false;
	}
	
	function combo_proveedores( $default = 0 )
	{
		global $conexion, $id_consorcio;
		
		$opciones	= "";
		$query		= "	SELECT	pro_id_proveedor AS id_proveedor,
								pro_id_consorcio AS id_consorcio,
								pro_nombres AS descripcion
						FROM	san_proveedores
						WHERE	pro_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['id_proveedor'] == $default )
					$opciones .= "<option selected value='$fila[id_proveedor]'>$fila[descripcion]</option>";
				else
					$opciones .= "<option value='$fila[id_proveedor]'>$fila[descripcion]</option>";
			}
		}
		
		return $opciones;
	}
	
	function combo_categorias( $default = 0 )
	{
		global $conexion, $id_consorcio;
		
		$opciones	= "";
		$query		= "	SELECT	cat_id_categoria AS id_categoria,
								cat_id_consorcio AS id_consorcio,
								cat_descripcion AS descripcion
						FROM	san_categorias
						WHERE	cat_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['id_categoria'] == $default )
					$opciones .= "<option selected value='$fila[id_categoria]'>$fila[descripcion]</option>";
				else
					$opciones .= "<option value='$fila[id_categoria]'>$fila[descripcion]</option>";
			}
		}
		
		return $opciones;
	}
	
	function combo_marcas( $default = 0 )
	{
		global $conexion, $id_consorcio;
		
		$opciones	= "";
		$query		= "	SELECT	mar_id_marca AS id_marca,
								mar_id_consorcio AS id_consorcio,
								mar_descripcion AS descripcion
						FROM	san_marcas
						WHERE	mar_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['id_marca'] == $default )
					$opciones .= "<option selected value='$fila[id_marca]'>$fila[descripcion]</option>";
				else
					$opciones .= "<option value='$fila[id_marca]'>$fila[descripcion]</option>";
			}
		}
		
		return $opciones;
	}
	
	function combo_dias( $p_dia )
	{
		$opciones	= "";
		
		for( $i = 1; $i <= 31; $i++ )
		{
			if( $i == $p_dia )
				$opciones .= "<option selected value='$i'>$i</option>";
			else
				$opciones .= "<option value='$i'>$i</option>";
		}
		
		return $opciones;
	}
	
	function combo_años( $año )
	{
		$opciones	= "";
		
		for( $i = 2015; $i <= 2025; $i++ )
		{
			if( $i == $año )
				$opciones .= "<option selected value='$i'>$i</option>";
			else
				$opciones .= "<option value='$i'>$i</option>";
		}
		
		return $opciones;
	}
	
	function combo_meses( $mes )
	{
		$opciones	= "";
		$meses		= array
		(
			'01'	=> 'Enero',
			'02'	=> 'Febrero',
			'03'	=> 'Marzo',
			'04'	=> 'Abril',
			'05'	=> 'Mayo',
			'06'	=> 'Junio',
			'07'	=> 'Julio',
			'08'	=> 'Agosto',
			'09'	=> 'Septiembre',
			'10'	=> 'Octubre',
			'11'	=> 'Noviembre',
			'12'	=> 'Diciembre'
		);
		
		foreach( $meses as $n_mes => $d_mes )
		{
			if( $n_mes == $mes )
				$opciones .= "<option selected value='$n_mes'>$d_mes</option>";
			else
				$opciones .= "<option value='$n_mes'>$d_mes</option>";
		}
		
		return $opciones;
	}
	
	function combo_cajeros( $default = 0, $activos = true )
	{
		global $conexion, $id_empresa, $id_consorcio, $id_usuario, $rol;
		
		$opciones	= "";
		$condicion	= "";
		
		if( $activos )
			$condicion .= " AND usua_status = 'A' ";
		
		if( $rol != 'S' )
			$condicion .= " AND usua_id_usuario = $id_usuario ";
		
		$query		= "	SELECT		usua_id_usuario AS id_usuario,
									CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) AS cajero
						FROM		san_usuarios
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = usua_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						WHERE		con_id_consorcio = $id_consorcio
						AND			(
										usua_id_empresa = $id_empresa
										OR 
										usua_rol = 'S'
										OR 
										usua_rol = 'M'
									)
									$condicion
						ORDER BY	cajero";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $default == $fila['id_usuario'] )
					$opciones .= "<option value='$fila[id_usuario]' selected>$fila[cajero]</option>";
				else
					$opciones .= "<option value='$fila[id_usuario]'>$fila[cajero]</option>";
			}
		}
		
		return $opciones;
	}
	
	function combo_sucursales( $default = 0 )
	{
		global $conexion, $id_consorcio;
		
		$opciones	= "";
		$query		= "	SELECT		emp_id_empresa AS id_empresa,
									emp_descripcion AS descripcion
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						WHERE		con_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $default == $fila['id_empresa'] )
					$opciones .= "<option value='$fila[id_empresa]' selected>$fila[descripcion]</option>";
				else
					$opciones .= "<option value='$fila[id_empresa]'>$fila[descripcion]</option>";
			}
		}
		else
			$opciones	.= "<option value=''>Error: ".mysqli_error( $conexion )."</option>";
			
		return $opciones;
	}
	
	//se utiliza para validar que los ID_SUCUrSALES pertenezcan al CONSORCIO cuando se captura
	function sucursales_por_empresa_validacion()
	{
		global $conexion, $id_consorcio;
		
		$ides		= array();
		$query		= "	SELECT		coem_id_empresa AS id_sucursal
						FROM		san_consorcios
						INNER JOIN	san_consorcio_empresa ON coem_id_consorcio = con_id_consorcio
						WHERE		con_id_consorcio = $id_consorcio";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $ides, $fila['id_sucursal'] );
		}
		
		return $ides;
	}
	
	function nuevo_folio( $par_tipo_folio = 'V' ) //V=Venta, A=Sist_apartado, C=Cotizaciones
	{
		global $conexion, $id_empresa;
		
		$año		= date( 'Y' );
		
		switch( $par_tipo_folio )
		{
			//V para la ventas
			case 'V':	$query = "CALL pro_nuevo_folio( $año, $id_empresa, @par_anio, @par_folio, @par_ticket, @par_impresora );";
						break;
			//A para el sistema de apartado
			case 'A':	$query = "CALL pro_nuevo_folio_sapartado( $año, $id_empresa, @par_anio, @par_folio, @par_ticket, @par_impresora );";
						break;
			//C para cotizaciones 
			case 'C':	$query = "CALL pro_nuevo_folio_cotizacion( $año, $id_empresa, @par_anio, @par_folio, @par_ticket, @par_impresora );";
						break;
		}
		
		$query		.= "SELECT	@par_anio AS anio, 
								@par_folio AS folio, 
								@par_ticket AS ticket,
								@par_impresora AS impresora";
		
		mysqli_autocommit( $conexion, false );
		
		if( mysqli_multi_query( $conexion, $query ) )
		{
			do
			{
				if( $resultado = mysqli_store_result( $conexion ) )
				{
					if( $fila = mysqli_fetch_assoc( $resultado ) )
						return $fila;
					
					mysqli_free_result( $resultado );
				}
			}
			while( mysqli_next_result( $conexion ) );
		}
		
		return false;
	}
	
	function getPDF_error( $mensaje )
	{
		// require_once( "fpdf/fpdf.php" );
		
		$pdf = new FPDF( 'P', 'mm', 'Letter' );
		$pdf -> AddPage();
		$pdf -> SetTitle("Ficha de Pago"); 
		$pdf -> AliasNbPages();
		$pdf -> SetMargins( 15, 30 );
		
		$pdf -> SetFont( 'Arial', '', 10 );
		$pdf -> SetTextColor( 222, 1, 1 );
		
		$pdf -> MultiCell( 0, 7, utf8_decode( $mensaje ), 0, 'C' );
		
		$pdf -> Output( "oops.pdf", 'I' );		//D
	}
	
	//conversion de DD-MM-YYYY, D-M-YYYY a YYYY-MM-DD, cuando se utiliza el DATEPICKER y se tiene que guardar en MYSQL
	function fecha_formato_mysql( $fecha )
	{
		$resultado	= '';
		
		if( $fecha )
		{
			$array_fecha = explode( '-', $fecha );
			
			if( count( $array_fecha ) == 3 )
			{
				list( $dia, $mes, $año ) = $array_fecha;
				
				if( checkdate( $mes, $dia, $año ) )
					$resultado = $año."-".$mes."-".$dia;
			}
		}
		
		return $resultado;
	}
	
	//MM-YYYY
	function fecha_a_mes( $fecha )
	{
		$mes	= "";
		
		$datos	= explode( '-', $fecha );
		
		switch( $datos[0] )
		{
			case '01': $mes = "Enero";		break;
			case '02': $mes = "Febrero";	break;
			case '03': $mes = "Marzo";		break;
			case '04': $mes = "Abril";		break;
			case '05': $mes = "Mayo";		break;
			case '06': $mes = "Junio";		break;
			case '07': $mes = "Julio";		break;
			case '08': $mes = "Agosto";		break;
			case '09': $mes = "Septiembre";	break;
			case '10': $mes = "Octubre";	break;
			case '11': $mes = "Noviembre";	break;
			case '12': $mes = "Diciembre";	break;
		}
		
		return $mes;
	}
	
	//DD-MM-YYYY
	function fecha_generica( $fecha, $p_dia = false )
	{
		$v_fecha	= "";
		
		if( $fecha )
		{
			list( $dia, $mes, $año ) = explode( '-', substr( $fecha, 0, 10 ) );
			$nmes		= '';
			$ndia		= '';
			$agregado	= substr( $fecha, 11 );
			
			if( checkdate( $mes, $dia, $año ) )
			{
				switch( $mes )
				{
					case 1:		$nmes = 'Enero'; 		break;
					case 2:		$nmes = 'Febrero';		break;
					case 3:		$nmes = 'Marzo'; 		break;
					case 4:		$nmes = 'Abril'; 		break;
					case 5:		$nmes = 'Mayo'; 		break;
					case 6:		$nmes = 'Junio'; 		break;
					case 7:		$nmes = 'Julio'; 		break;
					case 8:		$nmes = 'Agosto'; 		break;
					case 9:		$nmes = 'Septiembre'; 	break;
					case 10:	$nmes = 'Octubre'; 		break;
					case 11:	$nmes = 'Noviembre'; 	break;
					case 12:	$nmes = 'Diciembre'; 	break;
				}
				
				if( $p_dia )
				{
					$tdia	= strtotime( $fecha );
					$ddia	= date( 'D', $tdia );
					
					switch( $ddia )
					{
						case 'Sun':	$ndia = "Domingo";		break;
						case 'Mon':	$ndia = "Lunes";		break;
						case 'Tue':	$ndia = "Martes";		break;
						case 'Wed':	$ndia = "Miercoles";	break;
						case 'Thu':	$ndia = "Jueves";		break;
						case 'Fri':	$ndia = "Viernes";		break;
						case 'Sat':	$ndia = "Sábado";		break;
					}
					
					$v_fecha	= "$ndia, ";
				}
				
				if( $agregado )
					$v_fecha .= "$dia de $nmes de $año. $agregado";
				else
					$v_fecha .= "$dia de $nmes de $año";
			}
		}
		
		return $v_fecha;
	}
	
	function importe_a_letra($numero){
    
	$arreglo	= explode( '.', $numero );
	
	$numf = cienmiles($arreglo[0]);
    return $numf." PESOS $arreglo[1]/100 M.N.";
}

	function cienmiles($numcmero){
        if ($numcmero == 100000)
            $num_letracm = "CIEN MIL";
        if ($numcmero >= 100000 && $numcmero <1000000){
            $num_letracm = centena(Floor($numcmero/1000))." MIL ".(centena($numcmero%1000));        
        }
        if ($numcmero < 100000)
            $num_letracm = decmiles($numcmero);
        return $num_letracm;
    }
	
	function decmiles($numdmero){
        if ($numdmero == 10000)
            $numde = "DIEZ MIL";
        if ($numdmero > 10000 && $numdmero <20000){
            $numde = decena(Floor($numdmero/1000))."MIL ".(centena($numdmero%1000));        
        }
        if ($numdmero >= 20000 && $numdmero <100000){
            $numde = decena(Floor($numdmero/1000))." MIL ".(miles($numdmero%1000));     
        }       
        if ($numdmero < 10000)
            $numde = miles($numdmero);
        
        return $numde;
    }
	
	function miles($nummero){
        if ($nummero >= 1000 && $nummero < 2000){
            $numm = "MIL ".(centena($nummero%1000));
        }
        if ($nummero >= 2000 && $nummero <10000){
            $numm = unidad(Floor($nummero/1000))." MIL ".(centena($nummero%1000));
        }
        if ($nummero < 1000)
            $numm = centena($nummero);
        
        return $numm;
    }
	
	function centena($numc){
        if ($numc >= 100)
        {
            if ($numc >= 900 && $numc <= 999)
            {
                $numce = "NOVECIENTOS ";
                if ($numc > 900)
                    $numce = $numce.(decena($numc - 900));
            }
            else if ($numc >= 800 && $numc <= 899)
            {
                $numce = "OCHOCIENTOS ";
                if ($numc > 800)
                    $numce = $numce.(decena($numc - 800));
            }
            else if ($numc >= 700 && $numc <= 799)
            {
                $numce = "SETECIENTOS ";
                if ($numc > 700)
                    $numce = $numce.(decena($numc - 700));
            }
            else if ($numc >= 600 && $numc <= 699)
            {
                $numce = "SEISCIENTOS ";
                if ($numc > 600)
                    $numce = $numce.(decena($numc - 600));
            }
            else if ($numc >= 500 && $numc <= 599)
            {
                $numce = "QUINIENTOS ";
                if ($numc > 500)
                    $numce = $numce.(decena($numc - 500));
            }
            else if ($numc >= 400 && $numc <= 499)
            {
                $numce = "CUATROCIENTOS ";
                if ($numc > 400)
                    $numce = $numce.(decena($numc - 400));
            }
            else if ($numc >= 300 && $numc <= 399)
            {
                $numce = "TRESCIENTOS ";
                if ($numc > 300)
                    $numce = $numce.(decena($numc - 300));
            }
            else if ($numc >= 200 && $numc <= 299)
            {
                $numce = "DOSCIENTOS ";
                if ($numc > 200)
                    $numce = $numce.(decena($numc - 200));
            }
            else if ($numc >= 100 && $numc <= 199)
            {
                if ($numc == 100)
                    $numce = "CIEN ";
                else
                    $numce = "CIENTO ".(decena($numc - 100));
            }
        }
        else
            $numce = decena($numc);
        
        return $numce;  
}
	
	function decena($numdero){
    
        if ($numdero >= 90 && $numdero <= 99)
        {
            $numd = "NOVENTA ";
            if ($numdero > 90)
                $numd = $numd."Y ".(unidad($numdero - 90));
        }
        else if ($numdero >= 80 && $numdero <= 89)
        {
            $numd = "OCHENTA ";
            if ($numdero > 80)
                $numd = $numd."Y ".(unidad($numdero - 80));
        }
        else if ($numdero >= 70 && $numdero <= 79)
        {
            $numd = "SETENTA ";
            if ($numdero > 70)
                $numd = $numd."Y ".(unidad($numdero - 70));
        }
        else if ($numdero >= 60 && $numdero <= 69)
        {
            $numd = "SESENTA ";
            if ($numdero > 60)
                $numd = $numd."Y ".(unidad($numdero - 60));
        }
        else if ($numdero >= 50 && $numdero <= 59)
        {
            $numd = "CINCUENTA ";
            if ($numdero > 50)
                $numd = $numd."Y ".(unidad($numdero - 50));
        }
        else if ($numdero >= 40 && $numdero <= 49)
        {
            $numd = "CUARENTA ";
            if ($numdero > 40)
                $numd = $numd."Y ".(unidad($numdero - 40));
        }
        else if ($numdero >= 30 && $numdero <= 39)
        {
            $numd = "TREINTA ";
            if ($numdero > 30)
                $numd = $numd."Y ".(unidad($numdero - 30));
        }
        else if ($numdero >= 20 && $numdero <= 29)
        {
            if ($numdero == 20)
                $numd = "VEINTE ";
            else
                $numd = "VEINTI".(unidad($numdero - 20));
        }
        else if ($numdero >= 10 && $numdero <= 19)
        {
            switch ($numdero){
            case 10:
            {
                $numd = "DIEZ ";
                break;
            }
            case 11:
            {               
                $numd = "ONCE ";
                break;
            }
            case 12:
            {
                $numd = "DOCE ";
                break;
            }
            case 13:
            {
                $numd = "TRECE ";
                break;
            }
            case 14:
            {
                $numd = "CATORCE ";
                break;
            }
            case 15:
            {
                $numd = "QUINCE ";
                break;
            }
            case 16:
            {
                $numd = "DIECISEIS ";
                break;
            }
            case 17:
            {
                $numd = "DIECISIETE ";
                break;
            }
            case 18:
            {
                $numd = "DIECIOCHO ";
                break;
            }
            case 19:
            {
                $numd = "DIECINUEVE ";
                break;
            }
            }   
        }
        else
            $numd = unidad($numdero);
    return $numd;
}
	
	function unidad($numuero){
    switch ($numuero)
    {
        case 9:
        {
            $numu = "NUEVE";
            break;
        }
        case 8:
        {
            $numu = "OCHO";
            break;
        }
        case 7:
        {
            $numu = "SIETE";
            break;
        }       
        case 6:
        {
            $numu = "SEIS";
            break;
        }       
        case 5:
        {
            $numu = "CINCO";
            break;
        }       
        case 4:
        {
            $numu = "CUATRO";
            break;
        }       
        case 3:
        {
            $numu = "TRES";
            break;
        }       
        case 2:
        {
            $numu = "DOS";
            break;
        }       
        case 1:
        {
            $numu = "UN";
            break;
        }       
        case 0:
        {
            $numu = "";
            break;
        }       
    }
    return $numu;   
}
	
	
	//se debe checar en el corsorcio
	function existe_correo_socio( $correo, $id_socio = 0 )//se pasa id_socio cuando se actualiza
	{
		global $conexion;
		
		if( !$correo )
			return false;
		
		$query		= "	SELECT 	soc_id_socio 
						FROM 	san_socios 
						WHERE 	LOWER( soc_correo) = LOWER( '$correo' ) 
						AND 	soc_id_socio != $id_socio";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return true;
		}
		
		return false;
	}
	
	function existe_correo_usuario( $correo, $id_usuario = 0 )
	{
		global $conexion;
		
		if( !$correo )
			return false;
		
		$query		= "	SELECT usua_id_usuario FROM san_usuarios WHERE LOWER( usua_correo ) = LOWER( '$correo' ) AND usua_id_usuario != $id_usuario";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return true;
		}
		
		return false;
	}
	
	function tipo_archivo( $mime )
	{
		$extension	= '';
		
		switch( $mime )
		{
			case 'image/jpeg':	$extension = '.jpg'; break;
			case 'image/png':	$extension = '.png'; break;
		}
		
		return $extension;
	}
	
	function paginado( $par_filas, $seccion, $item = 'index' )
	{
		global $conexion, $gbl_paginado;
		
		$letra			= request_var( 'letra', '' );
		$pagina_actual	= request_var( 'pag', 1 );
		$bloque_actual	= request_var( 'blq', 1 );
		
		$pag_fechai		= request_var( 'pag_fechai', '' );
		$pag_fechaf		= request_var( 'pag_fechaf', '' );
		$pag_mes		= request_var( 'mes_calcular', '' );
		$pag_año		= request_var( 'año_calcular', '' );
		$pag_IDE		= request_var( 'pag_IDE', 0 );//id_empresa
		$pag_status		= request_var( 'pag_status', '' );
		$pag_proveedors	= request_var( 'pag_proveedores', 0 );
		$pag_categorias	= request_var( 'pag_categorias', 0 );
		$pag_marcas		= request_var( 'pag_marcas', 0 );
		$pag_opciones	= request_var( 'pag_opciones', 0 );
		$pag_busqueda	= request_var( 'pag_busqueda', '' );
		$pag_sucursal	= request_var( 'pag_sucursal', 0 );
		
		$var_blq_ant	= 1;
		$var_blq_sig	= 1;
		
		$var_bloques	= 1;
		$var_paginas	= 1;
		$var_pags_xblq	= 15;//paginas a mostrar en un bloque
		$var_datos		= '';
		$opciones		= '';
		
		if( $letra )
			$opciones .= "&letra=$letra";
		
		if( $pag_fechai )
			$opciones .= "&pag_fechai=$pag_fechai";
		
		if( $pag_fechaf )
			$opciones .= "&pag_fechaf=$pag_fechaf";
		
		if( $pag_mes )
			$opciones .= "&mes_calcular=$pag_mes";
		
		if( $pag_año )
			$opciones .= "&año_calcular=$pag_año";
		
		if( $pag_IDE )
			$opciones .= "&pag_IDE=$pag_IDE";
		
		if( $pag_status )
			$opciones .= "&pag_status=$pag_status";
		
		if( $pag_proveedors )
			$opciones .= "&pag_proveedores=$pag_proveedors";
		
		if( $pag_categorias )
			$opciones .= "&pag_categorias=$pag_categorias";
		
		if( $pag_marcas )
			$opciones .= "&pag_marcas=$pag_marcas";
		
		if( $pag_opciones )
			$opciones .= "&pag_opciones=$pag_opciones";
		
		if( $pag_busqueda )
			$opciones .= "&pag_busqueda=$pag_busqueda";
		
		if( $pag_sucursal )
			$opciones .= "&pag_sucursal=$pag_sucursal";
		
		if( $par_filas > $gbl_paginado && $gbl_paginado > 0 )
			$var_paginas = ceil( $par_filas / $gbl_paginado );
		
		if( $var_paginas > $var_pags_xblq && $var_pags_xblq )
			$var_bloques = ceil( $var_paginas / $var_pags_xblq );
		
		if( $var_paginas > 1 && $par_filas > $gbl_paginado )
		{
			$var_datos	= "	<hr/>
							<div class='row h6 text-center' id='paginado'>
								<div class='col-md-12'>
									<ul class='pagination'>";
			
			$bloque_actual	= ceil( $pagina_actual / $var_pags_xblq );
			
			$i = 1 + ( $var_pags_xblq * ( $bloque_actual - 1 ) );
			$j = 0;
			
			if( $bloque_actual > 1 & $bloque_actual <= $var_bloques )
				$var_blq_ant = $bloque_actual - 1;
			
			if( $bloque_actual >= 1 & $bloque_actual < $var_bloques )
				$var_blq_sig = $bloque_actual + 1;
			
			for( $i; $i <= $var_paginas; $i++ )
			{
				$j++;
				
				if( $j == 1 && $i >= $var_pags_xblq )
				{
					$var_pag_ant	= $i - 1;
					$var_datos		.= "<li><a href='.?s=$seccion&i=$item&blq=$var_blq_ant&pag=$var_pag_ant".$opciones."'>&laquo;</a></li>";
				}
				
				if( $pagina_actual == $i )
					$var_datos .= "<li class='active'><a href='.?s=$seccion&i=$item&blq=$bloque_actual&pag=$i".$opciones."'>".$i."</a></li>";
				else
					$var_datos .= "<li><a href='.?s=$seccion&i=$item&blq=$bloque_actual&pag=$i".$opciones."'>".$i."</a></li>";
				
				if( $j == $var_pags_xblq )
				{
					$var_pag_sig	= $i + 1;
					$var_datos 		.= "<li><a href='.?s=$seccion&i=$item&blq=$var_blq_sig&pag=$var_pag_sig".$opciones."'>&raquo;</a></li>";
					break;
				}
			}
			
			$var_datos	.= "		</ul>
								</div>
							</div>";
		}
		
		return $var_datos;
	}
	
	function validar_fecha( $date, $format = 'DD-MM-YYYY' )
	{
		if( strlen( $date ) >= 8 && strlen( $date ) <= 10 )
		{
			$separator_only	= str_replace( array( 'M','D','Y' ),'', $format ); // buscar, reemplaza, obj
			$separator		= $separator_only[0];
			
			if( $separator )
			{
				$regexp = str_replace( $separator, "\\" . $separator, $format );
				$regexp = str_replace( 'MM', '(0[1-9]|1[0-2])', $regexp );
				$regexp = str_replace( 'M', '(0?[1-9]|1[0-2])', $regexp );
				$regexp = str_replace( 'DD', '(0[1-9]|[1-2][0-9]|3[0-1])', $regexp );
				$regexp = str_replace( 'D', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp );
				$regexp = str_replace( 'YYYY', '\d{4}', $regexp );
				$regexp = str_replace( 'YY', '\d{2}', $regexp );
				
				if( $regexp != $date && preg_match( '/'.$regexp.'$/', $date ) )
				{
					foreach ( array_combine( explode( $separator,$format ), explode( $separator,$date ) ) as $key => $value )
					{
						if ( $key == 'YY') $year = '20'.$value;
						if ( $key == 'YYYY') $year = $value;
						if ( $key[0] == 'M') $month = $value;
						if ( $key[0] == 'D') $day = $value;
					}
					
					if( checkdate( $month,$day,$year ) )
						return true;
				}
			}
		}
		
		return false;
	}
	
	//solo funciona cuando se envia el formulario por POST
	function validar_php( $validar )
	{
		$exito['num']	= 1;
		$exito['msj']	= "<ul>";
		
		foreach( $validar as $campo => $valor )
		{
			if( isset( $_POST[ $campo ] ) && $_POST[ $campo ] && $_POST[ $campo ] != '-1' )
			{
				if( strlen( $_POST[ $campo ] ) > $valor['max'] )
				{
					$exito['num']	= 2;
					$exito['msj']	.= "<li>Para el campo: <strong>$valor[txt]</strong>, solo se permiten <strong>$valor[max]</strong> caracteres como máximo.</li>";
					continue;
				}
				
				if( $valor['tipo'] == 'N' && !is_numeric( $_POST[$campo] ) )
				{
					$exito['num']	= 2;
					$exito['msj']	.= "<li>Para el campo: <strong>$valor[txt]</strong>, solo se permite caracteres numéricos.</li>";
					continue;
				}
				
				if( $valor['tipo'] == 'C' && !strpos( $_POST[$campo], '@' ) )
				{
					$exito['num']	= 2;
					$exito['msj']	.= "<li>El correo que ha capturado para el campo <strong>$valor[txt]</strong>, no es válido.</li>";
					continue;
				}
				
				if( $valor['tipo'] == 'F' && !validar_fecha( $_POST[$campo], $valor['for'] ) )
				{
					$exito['num']	= 2;
					$exito['msj']	.= "<li>La fecha para el campo <strong>$valor[txt]</strong>, no es válido. Debe tener el siguiente formato: $valor[for]</li>";
					continue;
				}
			}
			elseif( $valor['req'] == 'S' )
			{
				$exito['num']	= 2;
				$exito['msj']	.= "<li>Llene o seleccione este campo para continuar: <strong>$valor[txt]</strong></li>";
				continue;
			}
		}
		
		$exito['msj']	.= "</ul>";
		
		unset( $valor );
		
		return $exito;
	}
	
	function mostrar_mensaje_div( $mensaje = ':)', $tipo = 'info' )
	{
		$alerta	= "";
		$titulo	= "";
		$icono	= "";
		
		switch( $tipo )
		{
			case 'success':
				$alerta = "alert alert-success";
				$titulo	= "Proceso completado";
				$icono	= "glyphicon glyphicon-ok-circle";
				break;
			
			case 'info':
				$alerta = "alert alert-info";
				$titulo	= "Mensaje de la página";
				$icono	= "glyphicon glyphicon-ok-circle";
				break;
				
			case 'danger':
				$alerta = "alert alert-danger";
				$titulo	= "Se encontró un problema y no se puede continuar";
				$icono	= "glyphicon glyphicon-ban-circle";
				break;
				
			case 'warning':
				$alerta = "alert alert-warning";
				$titulo	= "No se puede continuar";
				$icono	= "glyphicon glyphicon-ban-circle";
				break;
		}
		
		echo "	<div class='row'>
					<div class='col-md-12'>
						<div class='$alerta'>
							<p class='h5'>
								<span class='$icono'></span>
								<strong>$titulo</strong>
							</p>
							
							<hr/>
							
							<p>$mensaje</p>
						</div>
					</div>
				</div>";
	}
	
	function construir_insert( $tabla, $datos_sql )
	{
		$valores	= "";
		$columnas	= "";
		
		foreach( $datos_sql as $col => $dato )
		{
			$columnas	.= $col.", ";
			
			if( is_string( $dato ) )
			{
				$dato	= trim( $dato );
				$dato	= str_replace( "'", "''", $dato );
				
				$valores .= "'$dato', ";
			
			}
			else
				$valores .= "$dato, ";
		}
		
		$columnas 	= substr( $columnas, 0, -2 );
		$valores 	= substr( $valores , 0, -2 );
		$query		= "INSERT INTO $tabla( $columnas ) VALUES ( $valores )";
		
		return $query;
	}
	
	function construir_update( $tabla, $datos_sql, $condiciones )
	{
		$sentencia	= "";
		
		foreach( $datos_sql as $col => $dato )
		{
			if( is_string( $dato ) )
			{
				$dato	= trim( $dato );
				$dato	= str_replace( "'", "''", $dato );
				
				$sentencia .= sprintf( "%s = '%s', ", $col, $dato );
			}
			else
				$sentencia .= "$col = $dato, ";
		}
		
		$sentencia	= substr( $sentencia, 0, -2 );
		$query		= "UPDATE $tabla SET $sentencia WHERE $condiciones";
		
		return $query;
	}
	
?>