<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ExportController extends Controller
{
    // ...
    public function exportProductPdf(Product $product)
    {
        $pdf = \Pdf::loadView('emails.product_fiche', compact('product'));
        return $pdf->download('fiche-produit-'.$product->sku.'.pdf');
    }
}