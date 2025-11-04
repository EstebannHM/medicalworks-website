<?php
/**
 * Helper del carrito - contador items
 */

function getCartItemsCount() {
    //Crea sesion
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    
    //Retorna la columna de cantidad del carrito en sesion
    return array_sum(array_column($_SESSION['cart'], 'quantity'));
}
?>
