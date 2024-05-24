<?php
    $focus = "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
    <a class="list-group-item active" href=".?s=promociones"><span class="glyphicon glyphicon-gift"></span> Control de Promociones</a>
    <a class="list-group-item" href=".?s=promociones"><span class="glyphicon glyphicon-gift"></span> Lista de Promociones <?= ($item == 'index') ? $focus : '' ?></a>
    <a class="list-group-item" href=".?s=promociones&i=nuevo"><span class="glyphicon glyphicon-plus-sign"></span> Nueva Promoción <?= ($item == 'nuevo') ? $focus : '' ?></a>
</div>
