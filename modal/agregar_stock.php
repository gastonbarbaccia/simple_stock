<?php include("modal/registro_productos.php"); ?>

<form id="guardar_producto" class="form-horizontal" method="post" enctype="multipart/form-data" action="ajax/nuevo_producto.php">
  <div id="add-producto" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type='button' class="btn btn-success" data-toggle="modal" data-target="#add-producto">
  <span class="glyphicon glyphicon-plus"></span> Agregar
</button>

        </div>
        <div class="modal-body">

          
          <div class="form-group">
            <label for="codigo" class="col-sm-3 control-label">Código</label>
            <div class="col-sm-8">
              <input type="text" name="codigo" id="codigo" class="form-control" placeholder="Código" required>
            </div>
          </div>

          
          <div class="form-group">
            <label for="nombre" class="col-sm-3 control-label">Nombre</label>
            <div class="col-sm-8">
              <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre del producto" required>
            </div>
          </div>

          
          <div class="form-group">
            <label for="categoria" class="col-sm-3 control-label">Categoría</label>
            <div class="col-sm-8">
              <select name="categoria" id="categoria" class="form-control" required>
                <option value="">Seleccionar categoría</option>
                <option value="1">Categoría 1</option>
                <option value="2">Categoría 2</option>
              </select>
            </div>
          </div>

          
          <div class="form-group">
            <label for="precio" class="col-sm-3 control-label">Precio</label>
            <div class="col-sm-8">
              <input type="number" step="0.01" name="precio" id="precio" class="form-control" placeholder="Precio de venta" required>
            </div>
          </div>

          
          <div class="form-group">
            <label for="stock" class="col-sm-3 control-label">Stock</label>
            <div class="col-sm-8">
              <input type="number" min="0" name="stock" id="stock" class="form-control" placeholder="Stock inicial" required>
            </div>
          </div>

          
          <div class="form-group">
            <label for="imagen" class="col-sm-3 control-label">Imagen</label>
            <div class="col-sm-8">
              <input type="file" name="imagen" id="imagen" accept="image/*">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button id="nuevo_producto" type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
      </div>
    </div>
  </div>
</form>
