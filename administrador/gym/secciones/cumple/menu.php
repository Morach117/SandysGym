<?php
    $focus = "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
    <a class="list-group-item" href=".?s=cumple&i=ver"><span class="glyphicon glyphicon-calendar"></span> Ver cumpleañeros <?= ($item == 'ver') ? $focus : '' ?></a>
    <a class="list-group-item" href=".?s=cumple&i=modificar_descuento"><span class="glyphicon glyphicon-pencil"></span> Modificar descuento <?= ($item == 'modificar_descuento') ? $focus : '' ?></a>
</div>
