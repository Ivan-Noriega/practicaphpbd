<?php 	
/*en caso de que el usuario se devuelva del chat al registro tendra que dirijirse  al inicio para ingresar al chat si no desea crear otro usuario*/	
	require 'config.php';
	require 'funciones.php';
	

	if (!empty($_POST)) 
{ 
	$codigo_producto= $conexion->real_escape_string($_POST['codigo_producto']);
	/*$codigo_producto="'".$codigo_producto."'";*/
	$descripcion= $conexion->real_escape_string($_POST['descripcion']);
	$cantidad= $conexion->real_escape_string($_POST['cantidad']);
	$tipo_movimiento= $conexion->real_escape_string($_POST['tipo_movimiento']);
	$valor_movimiento= $conexion->real_escape_string($_POST['valor_movimiento']);
	$factura= $conexion->real_escape_string($_POST['factura']);
	$codigo_externo= $conexion->real_escape_string($_POST['codigo_externo']);
	$usuario= $conexion->real_escape_string($_POST['usuario']);
	date_default_timezone_set("america/bogota");
	$fecha_registro=date('y:m:d:h:i:s');

		if ($tipo_movimiento=='Abastecimiento' or $tipo_movimiento=='Devolucion' or $tipo_movimiento=='Venta' or $tipo_movimiento=='Averia' ) {
				
					$sql= "SELECT * FROM tb_disponibles WHERE cod_producto='$codigo_producto'";
					//echo $sql;
					include("config.php");
					$resultado=mysqli_query($conexion,$sql);
					$row=mysqli_fetch_row($resultado);

			if (!empty($row)) 
			{
				if ($row[4]=='Disponible' AND $tipo_movimiento=='Venta' AND $row[3]>0 or $row[4]=='Disponible' AND $tipo_movimiento=='Averia' AND $row[3]>0) 
				{	if ($row[3]>=$cantidad) 
					{

					$actualizar=$row[3]-$cantidad;
					$sql="update tb_disponibles set cantidad='$actualizar' where cod_producto='$codigo_producto' ";
					include("config.php");
					$resultado = $conexion->query( $sql );
					registromovimiento($descripcion, $cantidad, $tipo_movimiento, $valor_movimiento, $fecha_registro, $factura, $codigo_externo, $usuario, $codigo_producto);
					}else
					{
					echo "<script> alert ('No se puede registrar $tipo_movimiento, No hay esa cantidad del producto codigo $row[1] solo hay disponible: $row[4]') </script>";
					echo "<script>location.href='movimientos.php' </script>";
					}
				}
				// echo $row[4];
				// echo $tipo_movimiento;
				// echo $valor_movimiento;
				if ($row[4]=='Disponible' AND $tipo_movimiento=='Abastecimiento' AND $valor_movimiento==$row[6] AND $codigo_producto==$row[1]  or $row[4]=='Disponible' AND $tipo_movimiento=='Devolucion' AND $valor_movimiento==$row[6] AND $codigo_producto==$row[1])
				{
					$actualizar=$row[3]+$cantidad;
					$sql="update tb_disponibles set  cantidad='$actualizar', mostrar='1' where cod_producto='$codigo_producto' ";
					// echo $sql;
					include("config.php");
					$resultado = $conexion->query( $sql );

					registromovimiento($descripcion, $cantidad, $tipo_movimiento, $valor_movimiento, $fecha_registro, $factura, $codigo_externo, $usuario, $codigo_producto);

				}else
				{
					if ($valor_movimiento!==$row[6] AND $codigo_producto!==$row[1])  
					{
						$tipo_movimientopro="Disponible";
						$sql= "INSERT INTO tb_disponibles (cod_producto, descripcion, cantidad, estado, mostrar, precio_compra, fecha_registro) VALUES ('".$codigo_producto."','".$descripcion."','".$cantidad."','".$tipo_movimientopro."','1','".$valor_movimiento."','".$fecha_registro."')";
						// echo $sql;
						include "config.php";
						$conexion->query( $sql );
						registromovimiento($descripcion, $cantidad, $tipo_movimiento, $valor_movimiento, $fecha_registro, $factura, $codigo_externo, $usuario, $codigo_producto);	
						
					}else{
						echo "<script> alert ('No se puede registrar $tipo_movimiento, El codigo $row[1] esta asignado a otro producto') </script>";
						echo "<script>location.href='movimientos.php' </script>";
					}
				}

			}else
			{/*si el codigo_producto no se encuentra en tb_disponibles registrarlo en la misma*/
				if ($tipo_movimiento=="Abastecimiento" or $tipo_movimiento=="Devolucion" ) 
				{	
					$sql= "INSERT INTO tb_productos (cod_producto, descripcion, precio_compra, fecha_registro) VALUES ('".$codigo_producto."','".$descripcion."','".$valor_movimiento."','".$fecha_registro."')";
					include "config.php";
					$conexion->query( $sql );
					$tipo_movimientopro="Disponible";
					$sql= "INSERT INTO tb_disponibles (cod_producto, descripcion, cantidad, estado, mostrar, precio_compra, fecha_registro) VALUES ('".$codigo_producto."','".$descripcion."','".$cantidad."','".$tipo_movimientopro."','1','".$valor_movimiento."','".$fecha_registro."')";
					//echo $sql;
					include "config.php";
					$conexion->query( $sql );
					registromovimiento($descripcion, $cantidad, $tipo_movimiento, $valor_movimiento, $fecha_registro, $factura, $codigo_externo, $usuario, $codigo_producto);	
				}
				if ($tipo_movimiento=='Venta' or $tipo_movimiento=='Averia') 
				{ 	
					echo "<script> alert ('No se puede registrar $tipo_movimiento, El codigo $codigo_producto no se encuentra en Inventario') </script>";
					echo "<script>location.href='movimientos.php' </script>";		
				}
				
			}
			}else{
				if ($tipo_movimiento=="Solicitud de Garant??a" or $tipo_movimiento=="Llegada de Garant??a") {
					$sql= "SELECT * FROM tb_disponibles WHERE cod_producto='$codigo_producto' AND estado='$tipo_movimiento'";
					include("config.php");
				
					$resultado=mysqli_query($conexion,$sql);
					$row=mysqli_fetch_row($resultado);

					if (!empty($row)) 
					{ 	
						if($tipo_movimiento=="Solicitud de Garant??a" or $tipo_movimiento=="Llegada de Garant??a")
						{
						$actualizar=$row[3]+$cantidad;
						$sql="update tb_disponibles set cantidad='$actualizar', mostrar='1' where cod_producto='$codigo_producto' AND	estado='Llegada de Garant??a'";
						//echo $sql;
						include("config.php");
						$resultado = $conexion->query( $sql );

						registromovimiento($descripcion, $cantidad, $tipo_movimiento, $valor_movimiento, $fecha_registro, $factura, $codigo_externo, $usuario, $codigo_producto);
						}
					}else{
						
						if($tipo_movimiento=="Solicitud de Garant??a" or $tipo_movimiento=="Llegada de Garant??a")
						{
						$sql= "INSERT INTO tb_productos (cod_producto, descripcion, precio_compra, fecha_registro) VALUES ('".$codigo_producto."','".$descripcion."','".$valor_movimiento."','".$fecha_registro."')";
						include "config.php";
						$sql= "INSERT INTO tb_disponibles (cod_producto, descripcion, cantidad, estado, mostrar, precio_compra, fecha_registro) VALUES ('".$codigo_producto."','".$descripcion."','".$cantidad."','".$tipo_movimiento."','1','".$valor_movimiento."','".$fecha_registro."')";

						include "config.php";
						$conexion->query( $sql );
						registromovimiento($descripcion, $cantidad, $tipo_movimiento, $valor_movimiento, $fecha_registro, $factura, $codigo_externo, $usuario, $codigo_producto);
						}
						
					}
				}else{
					if ($tipo_movimiento=="Salida de Garant??a") {
						$tipoadisminuir="Solicitud de Garant??a";
					}
					if ($tipo_movimiento=="Entrega de Garant??a") {
						$tipoadisminuir="Llegada de Garant??a";
					}
					$sql= "SELECT * FROM tb_disponibles WHERE cod_producto='$codigo_producto' AND estado='$tipoadisminuir'";
						include("config.php");
				
						$resultado=mysqli_query($conexion,$sql);
						$row=mysqli_fetch_row($resultado);
					
					if($tipo_movimiento=="Salida de Garant??a" AND $row[3]>0 or $tipo_movimiento=="Entrega de Garant??a" AND $row[3]>0 )
					{
						

						
						if ($row[3]>=$cantidad)
						{
							$actualizar=$row[3]-$cantidad;
							$sql="update tb_disponibles set cantidad='$actualizar' where cod_producto='$codigo_producto' AND estado='$tipoadisminuir'";
						//echo $sql;
						include("config.php");
						$resultado = $conexion->query( $sql );
					
							if ($actualizar==0) 
							{	
								$sql="update tb_disponibles set mostrar='0' where cod_producto='$codigo_producto' AND estado='$tipoadisminuir' ";
								//$sql="DELETE FROM tb_disponibles WHERE cod_producto='$codigo_producto' AND estado='Solicitud de Garant??a'";
								include("config.php");
								$conexion->query( $sql );
							}
								registromovimiento($descripcion, $cantidad, $tipo_movimiento, $valor_movimiento, $fecha_registro, $factura, $codigo_externo, $usuario, $codigo_producto);
						}else
						{
							echo "<script> alert ('No se puede registrar $tipo_movimiento, Verifique la cantidad solicitada del producto codigo $row[0] solo hay: $row[3]') </script>";
							echo "<script>location.href='movimientos.php' </script>";
						}
						
					}else
						{
							echo "<script> alert ('No hay $tipoadisminuir del producto codigo $codigo_producto ') </script>";
							echo "<script>location.href='movimientos.php' </script>";
						}
						
					}
				}	
					
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php include ("inc/headcommon.php");	?>
	<title>GIBMAFE | Movimientos</title>
	<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
 		<script>
		$(document).ready(function(){
		$('#mitabla').DataTable({
		"order": [[1, "asc"]],
		"language":{
		"lengthMenu": "Mostrar _MENU_ registros por pagina",
		"info": "Mostrando pagina _PAGE_ de _PAGES_",
		"infoEmpty": "No hay registros disponibles",
		"infoFiltered": "(filtrada de _MAX_ registros)",
		"loadingRecords": "Cargando...",
		"processing":     "Procesando...",
		"search": "Buscar:",
		"zeroRecords":    "No se encontraron registros coincidentes",
		"paginate": {
		"next":       "Siguiente",
		"previous":   "Anterior"
		},					
		}
		});	
		});	
	</script>
</head>
<body> 
<?php
	/*manejando sesiones siempre va de primero el session para no mostrar el sitio si no hay un usuario conectado*/ 
	session_start();
	 include "config.php";
	// si el usuario esta conectado muestra el sitio de chat si no lo redirige al index para que se logee o se registre
	 	if (isset($_SESSION['usuario']))
	 	{
	include "inc/header.php";			
?>
<section>
	<div class="container">
		<div class="row">
					
				<div class="contenedor-menu col-xs-12 col-sm-2 col-sd-2 ">
				<div class="smenu ">
					<?php include("inc/menu.php"); ?> 	
				</div>
				</div>
				<div class="contenedor-section0	 col-xs-12 col-sm-10 col-sd-10 ">
				    
				    
				<div class="panel panel-success">
					
					<div class="panel-heading">
					    <div class="col-xs-12 col-sm-6 btn-group pull-right">
					   		<button type="button" class="col-xs-4 btn btn-success" data-toggle="modal" data-target="#nuevomovimiento"><span class="glyphicon glyphicon-plus"></span> Nuevo </button>
					    	<a href="pdf/reportemovimietos.php" target="_blank"><button type="button" class="col-xs-4 btn btn-danger" ><span class=" glyphicon glyphicon-print"></span> Imprimir </button></a>	
					    	<a href="pdf/ayudamov.pdf" target="_black"> <button type="button" style="float: right;"  class="col-xs-4 btn btn-default" ><span class="glyphicon glyphicon-question-sign"></span> Ayuda </button></a>	
						</div>
						
					</div>
					<div class="panel-body">

				<div class="col-xs-12 contenedor-section" ">
					<?php 
					$enlaceeli='eliminardato.php';	
					$tabla='tb_movimientos';
					$primarykey='cod_movimiento';
					$enlacefinal='movimientos.php';
					$sql="SELECT * FROM  tb_movimientos ORDER BY cod_movimiento DESC ";
					include("config.php");
					$resultado = $conexion->query( $sql );
					echo "	<table class='table table-condensed display' id='mitabla' > 
							<thead>
							<tr>	
									
									<th>#</th>	
									<th width='10'>Codigo Producto</th>	
									<th >Descripcion</th>
									<th width='10'>Stock</th>
									<th >Movimiento</th>
									<th >Valor</th>
									<th >Factura</th>
									<th >Externo</th>
									<th >Admin</th>
									<th >Fecha registro</th>	
									<th >Opciones</th>
									

							</tr>
							</thead>";

					while ($row=mysqli_fetch_row($resultado)) 
					{
						echo "
						
						<tr>
									<td align='center'>".$row[0]."</td>
									<td align='center'>".$row[9]."</td>
									<td>".$row[1]."</td>	
									<td align='center'>".$row[2]."</td>
									<td>".$row[3]."</td>
									<td align='center'>".$row[4]."</td>
									<td>".$row[6]."</td>
									<td align='center'>".$row[7]."</td>
									<td>".$row[8]."</td>
									<td>".$row[5]."</td>
									<td align='center'><a id='eliminarnegro' href='actualizarmovimiento.php?cod_movimiento=$row[0]' ><button class='glyphicon glyphicon-pencil'></button></a>
										<a id='eliminarnegro' href='javascript:mi_alerta()' ><button class='glyphicon glyphicon-trash'></button></a></td> 
								
							</tr>
							<script language='Javascript'>
								function mi_alerta()
								{
								confirmar=confirm('Esta Usted seguro que desea eliminar este producto');

								if (confirmar)
								{	
								location.href='$enlaceeli?codigo=$row[0]&tabla=$tabla&enlacefinal=$enlacefinal&primarykey=$primarykey';

								}


								}
								</script>
							";
					}
					echo "	</table>";	
					
				?>			<!--// <th>Editar</th>
									// <th>Eliminar</th>
				 // <td><a id='eliminarnegro' href='actualizarmovimiento.php?cod_movimiento=$row[0]' ><button class='glyphicon glyphicon-pencil'></button></a></td>
									// <td><a id='eliminarnegro' href='$enlaceeli?codigo=$row[0]&tabla=$tabla&enlacefinal=$enlacefinal&primarykey=$primarykey' ><button class='glyphicon glyphicon-trash'></button></a></td> -->
				</div>
																		<!-- Modal -->
							<div class="modal fade" id="nuevomovimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							  <div class="modal-dialog" role="document">
								<div class="modal-content">
								  <div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">??</span></button>
									<h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-edit"></i> Agregar nuevo Movimiento</h4>
								  </div>
								  <div class="modal-body">
									<form class="form-horizontal" method="post" id="guardar_vendedor" name="guardar_vendedor">
									<div id="resultados_ajax"></div>
										
										<div class="form-group">
										<label for="codigo_producto"  class="col-sm-3 control-label">Codigo del producto</label>
										<div class="col-sm-8">
										  <input type="text" pattern="[0-9]{1,11}" maxlength="11" class="form-control" id="codigo_producto" name="codigo_producto" required="">
										</div>
									  	</div>
									  
										<div class="form-group">
										<label for="Descripcion" class="col-sm-3 control-label">Descripcion</label>
										<div class="col-sm-8">
										  <input type="text"  class="form-control" id="descripcion" name="descripcion" required="">
										</div>
									  	</div>
									  
									  <div class="form-group">
										<label for="cantidad" class="col-sm-3 control-label">Cantidad</label>
										<div class="col-sm-8">
											<input type="text" pattern="[0-9]{1,5}" maxlength="5" class="form-control" id="cantidad" name="cantidad" required="">
										  
										</div>
									  </div>	 

									  <div class="form-group">
										<label for="tipo_movimiento" class="col-sm-3 control-label">Tipo de Movimiento</label>
										<div class="col-sm-8">
											<select class="form-control" name="tipo_movimiento" >	
											<option value="Venta">Venta</option>
											<option value="Abastecimiento">Abastecimiento</option>
											<option value="Averia">Aver??a</option>
											<option value="Devolucion">Devoluci??n</option>
											<option value="Solicitud de Garant??a">Solicitud de Garant??a</option>
											<option value="Salida de Garant??a">Salida de Garant??a</option>
											<option value="Llegada de Garant??a">Llegada de Garant??a</option>
											<option value="Entrega de Garant??a">Entrega de Garant??a</option>
											
											</select> 
										  
										</div>
									  </div>

									  <div class="form-group">
										<label for="valor_movimiento" class="col-sm-3 control-label">Valor Movimiento</label>
										<div class="col-sm-8">
											<input type="text" pattern="[0-9]{1,10}" maxlength="10" class="form-control" id="valor_movimiento" name="valor_movimiento" required="">
										</div>
									  </div>	

									  <div class="form-group">
										<label for="Factura" class="col-sm-3 control-label">Factura</label>
										<div class="col-sm-8">
											<input type="text" pattern="[a-zA-Z0-9]{1,15}" maxlength="15" class="form-control" id="Factura" name="factura" required="">
										  
										</div>
									  </div>	

									  <div class="form-group">
										<label for="codigo_externo" class="col-sm-3 control-label">Externo</label>
										<div class="col-sm-8">
										  	 <?php echo traer_lista_informacion( "codigo_externo", "tb_externos", "identificacion_externo", "nombre" ); ?>
										</div>
									  </div>	
									
									


										<div class="form-group">
										<label for="usuario" class="col-sm-3 control-label">Usuario</label>
										<div class="col-sm-8">
											<input type="text" pattern="[a-zA-Z0-9]{1,15}" maxlength="15" class="form-control" value="<?php echo $_SESSION['usuario']; ?>" id="usuario" name="usuario" required="" readonly>
										</div>
									  </div>	
								  </div>
								  <div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" class="btn btn-primary" name="enviar">Guardar datos</button>
									</form>
								  </div>
								  
								</div>
							  </div>
							</div>
													
				</div> 
				</div>
		</div>
	</div>
</section>
		<?php
		}else{	
			header("location: index.php");	
		 }
			include "inc/footer.php";
 		?>
 		
</body>
</html>