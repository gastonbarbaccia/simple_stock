<?php if (isset($con)) { ?>

<div class="modal fade" id="nuevoProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
		<h4 class="modal-title" id="myModalLabel">
		  <i class='glyphicon glyphicon-edit'></i> Agregar nuevo producto
		</h4>
	  </div>

	  <div class="modal-body">
		
		<div class="text-right">
		  <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#importarCSV">
			<span class="glyphicon glyphicon-upload"></span> Importar desde CSV
		  </button>
		</div>
		<br>

		<form class="form-horizontal" method="post" id="guardar_producto" name="guardar_producto" enctype="multipart/form-data">
		  <div id="resultados_ajax_productos"></div>

		  <div class="form-group">
			<label for="codigo" class="col-sm-3 control-label">Código</label>
			<div class="col-sm-8">
			  <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código del producto" required>
			</div>
		  </div>

		  <div class="form-group">
			<label for="nombre" class="col-sm-3 control-label">Nombre</label>
			<div class="col-sm-8">
			  <textarea class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto" required maxlength="255"></textarea>
			</div>
		  </div>

		  <div class="form-group">
			<label for="categoria" class="col-sm-3 control-label">Categoría</label>
			<div class="col-sm-8">
			  <select class='form-control' name='categoria' id='categoria' required>
				<option value="">Selecciona una categoría</option>
				<?php 
				  $query_categoria = mysqli_query($con, "SELECT * FROM categorias ORDER BY nombre_categoria");
				  while($rw = mysqli_fetch_array($query_categoria)) {
				?>
				  <option value="<?php echo $rw['id_categoria']; ?>"><?php echo $rw['nombre_categoria']; ?></option>
				<?php } ?>
			  </select>
			</div>
		  </div>

		  <div class="form-group">
			<label for="precio" class="col-sm-3 control-label">Precio</label>
			<div class="col-sm-8">
			  <input type="text" class="form-control" id="precio" name="precio" placeholder="Precio de venta del producto" required pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
			</div>
		  </div>

		  <div class="form-group">
			<label for="stock" class="col-sm-3 control-label">Stock</label>
			<div class="col-sm-8">
			  <input type="number" min="0" class="form-control" id="stock" name="stock" placeholder="Inventario inicial" required maxlength="8">
			</div>
		  </div>

		  <div class="form-group">
			<label for="imagen" class="col-sm-3 control-label">Imagen</label>
			<div class="col-sm-8">
			  <input type="file" name="imagen" id="imagen" accept="image/*" class="form-control">
			</div>
		  </div>

		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			<button type="submit" class="btn btn-primary" id="guardar_datos">Guardar datos</button>
		  </div>
		</form>
	  </div>
	</div>
  </div>
</div>

<!-- importar CSV -->
<div class="modal fade" id="importarCSV" tabindex="-1" role="dialog" aria-labelledby="importarLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <form method="post" id="form_importar_csv" enctype="multipart/form-data" action="ajax/importar_productos.php">
		<div class="modal-header">
		  <h4 class="modal-title" id="importarLabel">
			<i class="glyphicon glyphicon-upload"></i> Importar productos desde CSV
		  </h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
		</div>
		<div class="modal-body">
		  <div class="form-group">
			<label for="archivo_csv">Seleccionar archivo .csv</label>
			<input type="file" name="archivo_csv" id="archivo_csv" class="form-control" accept=".csv" required>
			<small class="text-muted">Formato: código,nombre,categoría,precio,stock</small>
		  </div>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
		  <button type="submit" class="btn btn-info">Importar</button>
		</div>
		
		<div id="resultados_ajax_importacion"></div>

	  </form>
	</div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 

<script>
$(document).ready(function(){
  $("#form_importar_csv").submit(function(e){
    e.preventDefault(); 

    var formData = new FormData(this);

    $.ajax({
      url: 'ajax/importar_productos.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response){
        
        $("#resultados_ajax_productos").html(response);

       
        $('#importarCSV').modal('hide');

        // refresca la pag en 5 seg para actualizar los productos
        setTimeout(function(){
          location.reload();
        }, 5000);
      },
      error: function(){
        $("#resultados_ajax_productos").html("<div class='alert alert-danger'>Error al procesar la importación.</div>");
      }
    });
  });
});
</script>


<?php } ?>
