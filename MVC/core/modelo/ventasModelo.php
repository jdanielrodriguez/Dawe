<?php


function  buscarCliente($nit)
{
    

    $mysql = conexionMysql();
    $form="";
    $sql = "SELECT nit,nombre,direccion,apellido,idcliente FROM cliente WHERE nit='$nit' or nombre='$nit'";
 
    if($resultado = $mysql->query($sql))
    {
      if($resultado->num_rows>0)
	  {
		$fila = $resultado->fetch_row();    
			
		
		$form .="<script>";
		$form .=" \$('#NIT').val('".$fila[0]."');\$('#NIT').focus();";
		$form .="/* document.getElementById('rol').value='".$fila[2]."';\$('#rol').focus();*/";
		$form .=" \$('#Cliente').val('".$fila[1]."');\$('#Cliente').focus();document.getElementById('Cliente').disabled=true;";
		$form .=" \$('#direccionC').val('".$fila[2]."');\$('#direccionC').focus();document.getElementById('direccionC').disabled=true;\$('#factura').focus();";
		$form .=" \$('#botonGuardar').show();";
		$form .=" \$('#botonNuevo').show();";
		$form .="iniciarVenta('".$fila[4]."'); ";
		
		$form .="</script>";
			
		$resultado->free();    
	  }
	  else
	  {
		$form .="<script>";
		$form .="\$('#modal4').openModal();
					llamarCliente(); ";
		$form .="</script>";
		$resultado->free();   
	  }
    
    }
    else
    {   
    
    $form = "<div><script>console.log('$idedit');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
    
}

function inicioVenta($idProv)
{
	$mysql = conexionMysql();
    $form="";
	session_start();
		$mysql->query("BEGIN");
		$mysql->query("delete from ventas where estado=2;");
   $sql = "INSERT INTO ventas(total,estado,tipoVenta,idCliente,nocomprobante,idusuario) values(0,2,'".$idProv[1]."','".$idProv[0]."',(select v.nocomprobante+1 from ventas v where v.estado=1 order by v.nocomprobante desc limit 1),'".$_SESSION['SOFT_USER_ID']."')";
 
    if($mysql->query($sql))
    {
		$sql = "SELECT idVentas,nocomprobante from ventas order by idVentas desc limit 1";
		//echo $sql;
		if($resultado = $mysql->query($sql))
    	{
      
			$fila = $resultado->fetch_row();    
				
				
					$_SESSION['idVenta']=$fila[0];
			
			 	$form .="<script>";
					$form .="document.getElementById('codigoVenta').value='".$_SESSION['idVenta']."';setTimeout(function(){\$('#tipoVenta').material_select();},0);document.getElementById('factura').value='".$fila[1]."';document.getElementById('factura').focus();";
				
				
				$form .="</script>";
		$mysql->query("COMMIT");
			$resultado->free();    
		}
		else
		{
			$mysql->query("ROLLBACK");
		}
    
    }
    else
    {   
    
    $form = "<div>$sql<script>console.log('$idProv[0]');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
  
}
function quitaInventario($datos)
{
	$mysql = conexionMysql();
    $form="";
	session_start();
	$mysql->query("BEGIN");
	
	$cont=$mysql->query("select cantidad from inventario where idproducto='".$datos[0]."'");
			 
				 $fila = $cont->fetch_row();
				 
				 if($fila[0]<$datos[1])
				 {
					 $datos[1]=$fila[0];
				 }
	if($datos[1]>0)
	{			 
		$total=$datos[3]*$datos[1];
			 
    $sql = "update inventario set cantidad=cantidad-".$datos[1]." where idproducto='".$datos[0]."'";
 	
    if($mysql->query($sql))
    {
			 
				 	
			//		 echo "<script>window.location.href = 'Ventas.php';/script>";
					echo '2';
			if(!$mysql->query("update ventas set estado=1 where idventas='".$_SESSION['idVenta']."'"))
					 {
						
						 $mysql->query("ROLLBACK");
					 }
					 else
					 if(!$mysql->query("update cuentasCobrar set estado=1 where idVentas='".$_SESSION['idVenta']."'"))
			 		{
				
				 		$mysql->query("ROLLBACK");
			 		}
					else
					if(!$mysql->query("update ventasdetalle set estado=1 where idventas='".$_SESSION['idVenta']."'"))
					 {
						
						 $mysql->query("ROLLBACK");
					 }
					 else
					 {	     
			 
			 
		
    					$mysql->query("COMMIT");
					}
		
			
    }
    else
    {   
    		$mysql->query("ROLLBACK");
    	$form = '1';
    
    }
	}
    else
	{
		echo "<script>window.location.reload();</script>";
	}
    
    $mysql->close();
    
    return printf($form);
}
function ingresoVenta($datos)
{
	$mysql = conexionMysql();
    $form="";
	session_start();
	$mysql->query("BEGIN");
	
	$cont=$mysql->query("select cantidad from inventario where idproducto='".$datos[0]."'");
			 
				 $fila = $cont->fetch_row();
				 
				 if($fila[0]<$datos[1])
				 {
					 $datos[1]=$fila[0];
				 }
	$total=$datos[6]*$datos[1];
	if($datos[1]>0)
	{				 
    $sql = "INSERT INTO ventasDetalle(cantidad,precio,estado,idventa,idproductos,subtotal) values('".$datos[1]."','".$datos[6]."',2,'".$_SESSION['idVenta']."',".$datos[0].",".$total.")";
 
    if($mysql->query($sql))
    {
			 
				 	  if(!$mysql->query("update ventas set total=total+".$total." where idventas='".$_SESSION['idVenta']."'"))
					 {
						
						 $mysql->query("ROLLBACK");
					 }
					 else
					 
					 if(!$mysql->query("update cuentasCobrar set total=total+".$total.",CreditoDado=CreditoDado+".$total." where idVentas='".$_SESSION['idVenta']."'"))
			 		{
				
				 		$mysql->query("ROLLBACK");
			 		}
					 else
					 {
						 echo "<script>cargarDetalleVentas('".$_SESSION['idVenta']."');document.getElementById('productosVenta').innerHTML='';document.getElementById('tipoVenta').disabled=true;$('#tipoVenta').material_select();limpiarProducto();</script>";
					 }
				     
			 
			 
		
    		$mysql->query("COMMIT");
		
			
    }
    else
    {   
    
    	$form = $sql;
    
    }
    }
    else
	{
		echo "<script>alert('Ya no hay en existencia del producto seleccionado');limpiarProducto();</script>";
	}
    
    $mysql->close();
    
    return printf($form);
  
}

function guardarVenta()
{
	$mysql = conexionMysql();
    $form="<script>location.reload();</script>";
	session_start();
    
   $mysql->query("delete from compras where idcompras='".$_SESSION['idCompra']."' and estado=2;");
    
    $mysql->close();
    
    return printf($form);
  
	
}

function  buscarProductoVenta($dato)
{
    

    $mysql = conexionMysql();
    $form="";
    $sql = "SELECT p.nombre,i.precioventa,p.idproductos,p.codigoProducto,i.cantidad FROM inventario i inner join productos p on p.idproductos=i.idproducto WHERE (p.nombre like '%".$dato[0]."%' or p.codigoProducto like '%".$dato[0]."%') and i.cantidad>1";
 
    if($resultado = $mysql->query($sql))
    {
      if($resultado->num_rows>0 && $dato[0]!="")
	  {
		  	
		while($fila = $resultado->fetch_row())
		{   
			
			$form .="<div class='borde' onClick=\"seleccionaProductoVenta('".$fila[2]."');\"><div><span>Codigo: </span><span class='enlinea' >".$fila[3]."</span></diV><div><span>Producto: </span><span class=' enlinea'>".$fila[0]."</span></div><div><span>Cantidad: </span><span class='enlinea' >".$fila[4]."</span></div></div><div><br>";
		}
		$resultado->free();    
	  }
	  else
	  {
		$form .="<script>";
			$form .="document.getElementById('agregarProd').hidden=false;";
			$form .="</script>";
	  }
    
    }
    else
    {   
    
    $form = "<div>$sql<script>console.log('".$dato[0]."');</script></div>";
    
    }
    
    
    $mysql->close();
    
    printf($form);
    
}

function  buscarPrecioProductoVenta($dato)
{
    

    $mysql = conexionMysql();
    $form="";
    $sql = "SELECT p.nombre,i.preciocosto,p.idproductos,p.codigoproducto,p.descripcion,i.precioCosto,i.precioVenta,i.precioClienteEs,i.precioDistribuidor,p.marca2,p.tipoRepuesto FROM inventario i inner join productos p on p.idproductos=i.idproducto WHERE p.idproductos='".$dato[0]."' ";
 	
    if($resultado = $mysql->query($sql))
    {
      if($resultado->num_rows>0)
	  {
		  if($fila = $resultado->fetch_row())
		  {
		  	$form .="<script>";
			$form .="document.getElementById('codigo').value='".$fila[2]."';\n";
			$form .="document.getElementById('Producto').value='".$fila[0]."';\ndocument.getElementById('Producto').focus();\n";
			$form .="document.getElementById('nombreC').value='".$fila[3]."';\ndocument.getElementById('nombreC').focus();\n";
			$form .="document.getElementById('descripcion').disabled=false;\ndocument.getElementById('descripcion').value='".$fila[4]."';\ndocument.getElementById('descripcion').focus();\ndocument.getElementById('descripcion').disabled=true;\n";
			$form .="document.getElementById('marca').disabled=false;\ndocument.getElementById('marca').value='".$fila[9]."';\ndocument.getElementById('marca').focus();\ndocument.getElementById('marca').disabled=true;\n";
			$form .="document.getElementById('tipoRepuesto').disabled=true;\n\$('#tipoRepuesto').val(\"".$fila[10]."\");\n$('#tipoRepuesto').material_select();\n";
			
			$form .="document.getElementById('precioM').disabled=false;\ndocument.getElementById('precioM').value='".($fila[5])."';\ndocument.getElementById('precioM').focus();\ndocument.getElementById('precioM').disabled=true;\n";
			$form .="document.getElementById('precioG').disabled=false;\ndocument.getElementById('precioG').value='".($fila[6])."';\ndocument.getElementById('precioG').focus();\ndocument.getElementById('precioG').disabled=true;\n";
			$form .="document.getElementById('precioE').disabled=false;\ndocument.getElementById('precioE').value='".($fila[7])."';\ndocument.getElementById('precioE').focus();\ndocument.getElementById('precioE').disabled=true;\n";
			$form .="document.getElementById('Cantidad').focus();\n";
			$form .="</script>";
			
		}
		$resultado->free();    
	  }
	  else
	  {
		$form .="<script>";
			$form .="document.getElementById('productosContenedor').hidden=true;";
			$form .="</script>";
	  }
    
    }
    else
    {   
    
    $form = "<div>$sql<script>console.log('".$dato[0]."');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
    
}

function buscarVenta($dato)
{
	

    $mysql = conexionMysql();
    $form="";
    $sql = "SELECT c.fecha,c.nocomprobante,p.nit,p.nombre,c.total,c.tipoventa,c.idventas,p.direccion FROM ventas c inner join cliente p on p.idcliente=c.idcliente where (c.estado=1 or c.estado=0) and c.idventas='".$dato[0]."' ";

    if($resultado = $mysql->query($sql))
    {
      if($resultado->num_rows>0)
	  {
		  if($fila = $resultado->fetch_row())
		  {
		  	$form .="<script>";
			$form .="document.getElementById('NIT').disabled=false;document.getElementById('NIT').value='".$fila[2]."';document.getElementById('NIT').focus();document.getElementById('NIT').disabled=true;";
			$form .="document.getElementById('fecha').disabled=false;document.getElementById('fecha').value='".substr($fila[0],0,10)."';document.getElementById('fecha').focus();document.getElementById('fecha').disabled=true;";
			$form .="document.getElementById('Cliente').disabled=false;document.getElementById('Cliente').value='".$fila[3]."';document.getElementById('Cliente').focus();document.getElementById('Cliente').disabled=true;";
			$form .="document.getElementById('direccionC').disabled=false;document.getElementById('direccionC').value='".$fila[7]."';document.getElementById('direccionC').focus();document.getElementById('direccionC').disabled=true;";
			$form .="document.getElementById('factura').disabled=false;document.getElementById('factura').value='".$fila[1]."';document.getElementById('factura').focus();document.getElementById('factura').disabled=true;";
			//$form .="document.getElementById('tipoVenta').disabled=false;document.getElementById('tipoVenta').value='".$fila[5]."'.selected;document.getElementById('tipoVenta').focus();document.getElementById('tipoVenta').disabled=true;";
			$form .="\$('#tipoVenta').val(\"".$fila[5]."\");$('select').material_select(); ";
			$form .="cargarDetalleVentas('".$dato[0]."');";
			$form .="</script>";
			
		}
		$resultado->free();    
	  }
	  else
	  {
		$form .="<script>";
			$form .="document.getElementById('productosContenedor').hidden=true;";
			$form .="</script>";
	  }
    
    }
    else
    {   
    
    $form = "<div>$sql<script>console.log('".$dato[0]."');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
    
}

function cambiarTipoVenta($datos)
{
	$mysql = conexionMysql();
    $form="";
	
		$mysql->query("BEGIN");
    $sql = "update ventas set tipoVenta='".$datos[0]."' where idventas='".$datos[1]."'";
//echo $sql;
    if($mysql->query($sql))
    {
		
		$mysql->query("COMMIT");
			    
		
    
    }
    else
    {   
    	$mysql->query("ROLLBACK");
    $form = "<div><script>console.log('".$datos[0]."');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
}
function agregarFacturaVenta($datos)
{
	$mysql = conexionMysql();
    $form="";
	
		$mysql->query("BEGIN");
    $sql = "update ventas set NoComprobante='".$datos[0]."' where idventas='".$datos[1]."'";
//echo $sql;
    if($mysql->query($sql))
    {
		
		$mysql->query("COMMIT");
			    
		
    
    }
    else
    {   
    	$mysql->query("ROLLBACK");
    $form = "<div><script>console.log('".$datos[0]."');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
}

function anularVenta($datos)
{
	$mysql = conexionMysql();
    $form="";
	
		$mysql->query("BEGIN");
    $sql = "update ventas set estado='0' where idventas='".$datos[0]."'";
//echo $sql;
    if($mysql->query($sql))
    {
		if(!$mysql->query("update ventasdetalle set estado='0' where idventa='".$datos[0]."'"))
				{
					$mysql->query("ROLLBACK");
				}
				else
		if($con=$mysql->query("select cantidad,idproductos from ventasdetalle where idventa='".$datos[0]."'"))
    	{
			while($fila = $con->fetch_row())
			{
				if(!$mysql->query("update inventario set cantidad=cantidad+".$fila[0]." where idproducto='".$fila[1]."'"))
				{
					$mysql->query("ROLLBACK");
				}
			}
			
			$mysql->query("COMMIT");
		}
		else
		{
			$mysql->query("ROLLBACK");
		}
		
			    
		$form = "<script>alert('La Venta fue anulada');setTimeout(window.location.reload(), 3000);</script>";
    
    }
    else
    {   
    	$mysql->query("ROLLBACK");
    $form = "<div><script>location.reload();console.log('".$datos[0]."');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
}
function anularDetalleVenta($datos)
{
	$mysql = conexionMysql();
    $form="";
	session_start();
		$mysql->query("BEGIN");
    $sql = "delete from ventasdetalle where idventadetalle='".$datos[0]."'";
//echo $sql;
    if($mysql->query($sql))
    {
		
		$mysql->query("COMMIT");
			    
		$form = "<script>cargarDetalleVentas('".$_SESSION['idVenta']."');</script>";
    
    }
    else
    {   
    	$mysql->query("ROLLBACK");
    	$form = "<div><script>cargarDetalleVentas('".$_SESSION['idVenta']."');</script></div>";
    
    }
    
    
    $mysql->close();
    
    return printf($form);
}



?>