<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .box { background: #f3f4f6; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .price-tag { font-size: 18px; font-weight: bold; color: #111827; }
        .footer { margin-top: 50px; font-size: 10px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BON DE RECOMMANDATION</h1>
        <p>Réf : {{ $order_number }} | Date : {{ $date }}</p>
    </div>

    <h3>Produit : {{ $product->name }}</h3>
    <p>SKU : {{ $product->sku }} | Prix unitaire : {{ number_format($product->price, 2, ',', ' ') }} €</p>

    <div class="box">
        <p><strong>Stock actuel :</strong> {{ $product->quantity }} unités</p>
        <p><strong>Autonomie :</strong> {{ $prediction['jours_restants'] }} jours restants</p>
        <hr style="border: 0; border-top: 1px solid #d1d5db; margin: 15px 0;">
        <p><strong>Quantité conseillée :</strong> {{ $product->min_stock * 2 }} unités</p>
        <p><strong>Coût total estimé :</strong> <span class="price-tag">{{ number_format(($product->min_stock * 2) * $product->price, 2, ',', ' ') }} €</span></p>
        <p style="color: #e11d48; margin-top: 10px;"><strong>Action :</strong> {{ $prediction['recommandation'] }}</p>
    </div>

    <div class="footer">Généré par StockManager Pro - Analyse prédictive des stocks</div>
</body>
</html>