<?php


if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('showProductDetails')) {
    function showProductDetails(?array $product): void
    {
        if (empty($product) || !isset($product['id_product'])) return;

        $id = (int) ($product['id_product']);
        $name = h($product['name'] ?? '');
        $image = h($product['image_path'] ?? 'placeholder.jpg');
        $sku = isset($product['sku']) ? h($product['sku']) : 'MED-' . str_pad($id, 3, '0', STR_PAD_LEFT);
        $descriptionRaw = $product['description'] ?? '';
        $fullDescription = h($descriptionRaw);
        
        $short = mb_strlen($descriptionRaw) > 100
            ? mb_substr($descriptionRaw, 0, 100) . '...'
            : $descriptionRaw;
        $shortDescription = h($short);



?>
    <div class="product-detail" data-product-id="<?php echo $id; ?>">

            
            <div class="product-media">
              
                <div class="product-image">
                    <img src="../assets/img/<?php echo $image; ?>" alt="<?php echo $name; ?>" loading="lazy">
                </div>

               
                <div class="product-accordion">
                    <details class="accordion-item">
                        <summary>Detalles</summary>
                        <div class="accordion-content">
                            <p><?php echo $fullDescription !== '' ? $fullDescription : 'Sin descripción.'; ?></p>
                        </div>
                    </details>

                    <details class="accordion-item">
                        <summary>Ingredientes</summary>
                        <div class="accordion-content">
                            <ul>
                                <li>Ingrediente 1</li>
                                <li>Ingrediente 2</li>
                                <li>Ingrediente 3</li>
                            </ul>
                        </div>
                    </details>
                </div>
            </div>

            
            <div class="product-info">
                <div class="product-header">
                    <span class="product-id">SKU: <?php echo $sku; ?></span>
                    <h3 class="product-name">
                        <?php echo $name; ?>
                    </h3>
                    <p class="product-description"><?php echo $shortDescription; ?></p>
                </div>

                <div class="product-actions">
                    <div class="quantity-controls">
                        <label for="quantity-<?php echo $id; ?>">Cantidad:</label>
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn qty-decrease" data-product-id="<?php echo $id; ?>">-</button>
                            <input
                                id="quantity-<?php echo $id; ?>"
                                class="qty-input"
                                type="number"
                                value="1"
                                min="1"
                                max="999"
                                data-product-id="<?php echo $id; ?>">
                            <button type="button" class="qty-btn qty-increase" data-product-id="<?php echo $id; ?>">+</button>
                        </div>
                    </div>

                    <button type="button" class="btn-add-cart" data-product-id="<?php echo $id; ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                        </svg>
                        Agregar al carrito
                    </button>
                </div>
            </div>
        </div>
<?php
    }
}
?>

<?php

if (!function_exists('showProductDetailsById')) {
    function showProductDetailsById(?PDO $pdo, ?int $id): void
    {
       
        if ($pdo === null) {
            echo '<p style="text-align:center;">Error: conexión a la base de datos no disponible.</p>';
            return;
        }

        $idInt = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idInt === false) {
            echo '<p style="text-align:center;">ID de producto inválido.</p>';
            return;
        }

      
        require_once __DIR__ . '/../api/product.php';
        $product = getProductById($pdo, $idInt);
        if ($product === null) {
            echo '<p style="text-align:center;">Producto no encontrado.</p>';
            return;
        }

       
        showProductDetails($product);
    }
}

?>