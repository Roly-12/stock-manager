<style>
    body { 
        font-family: 'Helvetica', 'Arial', sans-serif; 
        color: #333;
        font-size: 12px;
    }
    header { 
        border-bottom: 2px solid #4F46E5; 
        padding-bottom: 10px; 
        margin-bottom: 20px; 
    }
    .logo-text { 
        font-size: 24px; 
        font-weight: bold; 
        color: #4F46E5; 
    }
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 20px; 
    }
    th { 
        background-color: #4F46E5; 
        color: white; 
        text-transform: uppercase; 
        font-size: 10px; 
        padding: 10px;
    }
    td { 
        border-bottom: 1px solid #eee; 
        padding: 10px; 
    }
    tr:nth-child(even) { background-color: #f9fafb; }
    
    .status-badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: bold;
    }
    .low-stock { background-color: #fee2e2; color: #ef4444; }
    
    .total-box { 
        margin-top: 30px; 
        padding: 15px; 
        background-color: #1e293b; 
        color: white; 
        text-align: right; 
        border-radius: 8px;
    }
</style>

<h2>Rapport d'état des stocks</h2>
<p>Généré le : {{ $date }}</p>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Désignation</th>
            <th>Stock</th>
            <th>Prix Unit.</th>
            <th>Valeur Totale</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $p)
        <tr>
            <td><strong>{{ $p->sku }}</strong></td>
            <td>{{ $p->name }}</td>
            <td>
                {{ $p->quantity }}
                @if($p->quantity <= $p->min_stock)
                    <span class="status-badge low-stock">ALERTE</span>
                @endif
            </td>
            <td>{{ number_format($p->price, 2) }} €</td>
            <td>{{ number_format($p->quantity * $p->price, 2) }} €</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total">Valeur totale du stock : {{ number_format($totalValue, 2) }} €</div>