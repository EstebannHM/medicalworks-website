<?php
/**
 * Repositorio para guardar cotizaciones en BD - Repository Pattern
 */

class QuoteRepository 
{
    private PDO $pdo;
    
    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Guarda una cotización completa (quote + quote_details)
     * 
     * @param array $userData ['fullName', 'email', 'phone']
     * @param array $products Lista de productos -> Carrito
     * @param string $quoteFilename Nombre del archivo PDF generado de la cotizacion
     * 
     * Rollback por si alguna consulta falla.
     * 
     */
    
    public function saveQuote(array $userData, array $products, string $quoteFilename): int 
    {
        try {
            $this->pdo->beginTransaction();
            
            // 1. Insertar en tabla quotes
            $quoteId = $this->insertQuote($userData, $quoteFilename);
            
            // 2. Insertar cada producto en quote_details
            $this->insertQuoteDetails($quoteId, $products);
            
            $this->pdo->commit();
            
            return $quoteId;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error guardando cotización: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Inserta el registro principal de la cotización
     */
    private function insertQuote(array $userData, string $quoteFilename): int 
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO quotes (client_name, client_email, client_phone, quote_Filename, created_at)
            VALUES (:name, :email, :phone, :pdf, CURRENT_TIMESTAMP)
        ");
        
        $stmt->execute([
            'name' => $userData['fullName'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
            'pdf' => $quoteFilename
        ]);
        
        return (int) $this->pdo->lastInsertId();
    }
    
    /**
     * Inserta los productos de la cotización
     */
    private function insertQuoteDetails(int $quoteId, array $products): void 
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO quote_details (id_quote, id_product, quantity)
            VALUES (:quote_id, :product_id, :quantity)
        ");
        
        foreach ($products as $product) {
            $stmt->execute([
                'quote_id' => $quoteId,
                'product_id' => $product['id'],
                'quantity' => $product['quantity']
            ]);
        }
    }
}
