<?php
if (!isset($_POST["submit"])) {
    header("Location:index.php");
    exit;
}

if (isset($_POST["submit"])) {
    $id_usuario = htmlspecialchars($_POST["id_usuario"]);
    $total = (float) htmlspecialchars($_POST["total_usuario"]);
}

try {
    require "inc/funciones/conexionbd.php";
    
    $sql = "SELECT * FROM carritos WHERE id_usuario = $id_usuario";
    $respuesta = $conn->query($sql);

    $i = 0;
    $arreglo_pedido = array();
    
    while ($carrito = $respuesta->fetch_assoc()) {
        $producto = json_decode($carrito["producto_carrito"]);

        ${"articulo$i"} = new stdClass();
        $arreglo_pedido[] = ${"articulo$i"};
        ${"articulo$i"}->name = $producto->nombre_producto;
        ${"articulo$i"}->currency = "USD";
        ${"articulo$i"}->quantity = $producto->cantidad;
        ${"articulo$i"}->price = $producto->precio_producto;

        $i++;
    }

    // Simula la finalización del pago y actualiza la base de datos
    $id_registro = uniqid();
    $estado_pago = 'completed'; // Estado del pago simulado

    $sql_insert = "INSERT INTO pagos (id_pago, id_usuario, total, estado) VALUES ('$id_registro', '$id_usuario', '$total', '$estado_pago')";
    $conn->query($sql_insert);

    // Limpia el carrito después del pago
    $sql_delete = "DELETE FROM carritos WHERE id_usuario = $id_usuario";
    $conn->query($sql_delete);

    echo "<script>alert('El pago se ha procesado exitosamente.'); window.location.href = 'pago-finalizado.php?id_pago={$id_registro}&total_usuario={$total}';</script>";
    exit;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
