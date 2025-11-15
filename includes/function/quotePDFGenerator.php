<?php
/**
 * Generador de PDFs para cotizaciones
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class QuotePDFGenerator 
{
    private const PAPER_SIZE = 'Letter';
    private const ORIENTATION = 'portrait';
    
    private Dompdf $dompdf;
    private string $pdfDirectory;
    
    public function __construct() 
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        
        $this->dompdf = new Dompdf($options);
        $this->pdfDirectory = __DIR__ . '/../../pdfs/';
        
        //Crear carpeta si no existe
        if (!is_dir($this->pdfDirectory)) {
            mkdir($this->pdfDirectory, 0755, true);
        }
    }
    
    /**
     * Genera PDF de cotización
     */
    public function generateQuotePDF(array $userData, array $products): array 
    {
        try {
            
            $html = $this->buildHTML($userData, $products);
            
            $this->dompdf->loadHtml($html);
            $this->dompdf->setPaper(self::PAPER_SIZE, self::ORIENTATION);
            $this->dompdf->render();
            
            $filename = $this->generateFilename();
            $filepath = $this->pdfDirectory . $filename;
            
            file_put_contents($filepath, $this->dompdf->output());
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'message' => 'PDF generado correctamente'
            ];
            
        } catch (Exception $e) {
            error_log("Error generando PDF: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function generateFilename(): string
    {
        $timestamp = date('Ymd_His');
        $hash = bin2hex(random_bytes(4)); // 8 caracteres hex
        return "quote_{$timestamp}_{$hash}.pdf";
    }
    
    private function buildHTML(array $userData, array $products): string 
    {
        $company = $this->getCompanyInfo();
        $date = date('d/m/Y H:i:s');
        $stats = [
            'total' => count($products),
            'quantity' => array_sum(array_column($products, 'quantity'))
        ];
        
        // Escapar datos
        $esc = fn($str) => htmlspecialchars($str);
        
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Cotización - Medical Works</title>
            <style>{$this->getStyles()}</style>
        </head>
        <body>
            <div class="header">
                <div class="company-info">
                    <h1>{$esc($company['name'])}</h1>
                    <p>{$esc($company['address'])}</p>
                    <p>Tel: {$esc($company['phone'])} | Email: {$esc($company['email'])}</p>
                </div>
                <div class="quote-info">
                    <h2>COTIZACIÓN</h2>
                    <p><strong>Fecha:</strong> {$date}</p>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="customer-info">
                <h3>Datos del Cliente</h3>
                <table class="info-table">
                    <tr><td><strong>Nombre:</strong></td><td>{$esc($userData['fullName'])}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>{$esc($userData['email'])}</td></tr>
                    <tr><td><strong>Teléfono:</strong></td><td>{$esc($userData['phone'])}</td></tr>
                </table>
            </div>
            
            <div class="products-section">
                <h3>Productos Solicitados</h3>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$this->buildProductRows($products)}
                    </tbody>
                </table>
            </div>
            
            <div class="summary">
                <p><strong>Total de productos únicos:</strong> {$stats['total']}</p>
                <p><strong>Cantidad total de unidades:</strong> {$stats['quantity']}</p>
            </div>
            
            <div class="footer">
                <p>Esta es una cotización preliminar. Un asesor se pondrá en contacto con usted para confirmar precios y disponibilidad.</p>
                <p class="footer-note">Documento generado automáticamente por Medical Works</p>
            </div>
        </body>
        </html>
        HTML;
    }
    
    private function buildProductRows(array $products): string 
    {
        $rows = array_map(function($product) {
            $sku = $product['sku'];
            $name = htmlspecialchars($product['name']);
            $qty = intval($product['quantity']);
            
            return "<tr><td>{$sku}</td><td>{$name}</td><td class='text-center'>{$qty}</td></tr>";
        }, $products);
        
        return implode("\n", $rows);
    }
    
    private function getCompanyInfo(): array 
    {
        return [
            'name' => 'Medical Works',
            'address' => 'Costa Rica, San José, Aserrí
                            De la esquina noreste de la iglesia católica 500
                            metros norteste.',
            'phone' => '+506 2230-8023',
            'email' => 'info@medworkcr.com'
        ];
    }
    
    private function getStyles(): string 
    {
        return '
            * { 
                margin: 0; 
                padding: 0; 
                box-sizing: border-box;
            }
            
            body {
                font-size: 11pt;
                color: #333;
                padding: 30px;
                line-height: 1.5;
            }
            
            .header { display: table; width: 100%; margin-bottom: 20px; }
            .company-info { display: table-cell; width: 60%; vertical-align: top; }
            .company-info h1 { font-size: 20pt; color: #0EA5E9; margin-bottom: 8px; }
            .company-info p { font-size: 9pt; color: #666; margin: 2px 0; }
            
            .quote-info { display: table-cell; width: 40%; text-align: right; vertical-align: top; }
            .quote-info h2 { font-size: 18pt; color: #1E293B; margin-bottom: 8px; }
            .quote-info p { font-size: 9pt; color: #666; }
            
            .divider { height: 2px; background: #0EA5E9; margin: 20px 0; }
            
            .customer-info, .products-section { margin-bottom: 25px; }
            
            h3 {
                font-size: 13pt;
                color: #1E293B;
                margin-bottom: 12px;
                padding-bottom: 5px;
                border-bottom: 1px solid #E2E8F0;
            }
            
            .info-table { width: 100%; border-collapse: collapse; }
            .info-table td { padding: 6px 0; font-size: 10pt; }
            .info-table td:first-child { width: 120px; color: #64748B; }
            
            .products-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .products-table thead { background: #F1F5F9; }
            .products-table th {
                padding: 10px;
                text-align: left;
                font-size: 10pt;
                font-weight: 600;
                color: #1E293B;
                border: 1px solid #E2E8F0;
            }
            .products-table td { padding: 10px; font-size: 10pt; border: 1px solid #E2E8F0; }
            .products-table tbody tr:nth-child(even) { background: #F8FAFC; }
            
            .text-center { text-align: center; }
            
            .summary { background: #F1F5F9; padding: 15px; border-radius: 5px; margin: 20px 0; }
            .summary p { margin: 5px 0; font-size: 10pt; }
            
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #E2E8F0; }
            .footer p { font-size: 9pt; color: #64748B; margin: 5px 0; text-align: center; }
            .footer-note { font-style: italic; color: #94A3B8; }
        ';
    }
}