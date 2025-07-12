<form class="form-horizontal" method="post">
  <!-- Modal -->
  <div id="remove-stock" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title">Eliminar Stock</h4>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label for="quantity" class="col-sm-2 control-label">Cantidad</label>
            <div class="col-sm-6">
              <input type="number" min="1" name="quantity_remove" class="form-control" id="quantity_remove" value="" placeholder="Cantidad" required="">
            </div>
          </div>
          <div class="form-group">
            <label for="reference_remove" class="col-sm-2 control-label">Motivo</label>
            <div class="col-sm-6">
              <select name="reference_remove" class="form-control" id="reference_remove" required>
                <option value="">Seleccione un motivo</option>
                <option value="Eliminicación por venta">Por venta</option>
                <option value="Otros">Otros motivos</option>
              </select>
            </div>
          </div>
          <div class="form-group" id="precio_group">
            <!-- Label original -->
            <label id="label_precio" for="reference_remove_2_visible" class="col-sm-2 control-label">Precio</label>
            <!-- Label alternativo para mostrar si elige "otros" -->
            <label id="label_motivo_adicional" class="col-sm-2 control-label" style="display: none;padding-top:15px">Detalle</label>

            <div class="col-sm-6">
              <!-- Select visible por defecto -->
              <select class="form-control" id="reference_remove_2_select">
                <option value="">Seleccione un tipo de precio</option>
                <option value="<?php echo number_format($row['precio_producto_cons_final'], 2); ?>">
                  Precio consumidor final $ <?php echo number_format($row['precio_producto_cons_final'], 2); ?>
                </option>
                <option value="<?php echo number_format($row['precio_producto_reventa'], 2); ?>">
                  Precio reventa $ <?php echo number_format($row['precio_producto_reventa'], 2); ?>
                </option>
              </select>

              <!-- Input de precio/motivo personalizado -->
              <input type="text" class="form-control" id="reference_remove_2_input" style="display:none; margin-top:10px;" placeholder="Detalle el motivo..." />

              <!-- Campo oculto que se envía -->
              <input type="hidden" name="reference_remove_2" id="reference_remove_2" required />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar datos</button>
        </div>
      </div>

    </div>
  </div>
</form>