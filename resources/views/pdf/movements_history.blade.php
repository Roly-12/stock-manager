<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #334155; font-size: 11px; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 10px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        td { padding: 10px; border-bottom: 1px solid #f1f5f9; }
        .badge { padding: 3px 7px; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .in { background: #dcfce7; color: #15803d; }
        .out { background: #fee2e2; color: #b91c1c; }
        .qty { font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Journal des Flux de Stock</div>
        <p>Type : {{ ucfirst($type) }} | Date d'extraction : {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Horodatage</th>
                <th>Produit & SKU</th>
                <th>Type</th>
                <th style="text-align: right">Quantité</th>
                <th>Motif / Raison</th>
                <th>Auteur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $mvt)
            <tr>
                <td>{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <strong>{{ $mvt->product->name ?? 'Produit supprimé' }}</strong><br>
                    <small>{{ $mvt->product->sku ?? '-' }}</small>
                </td>
                <td>
                    <span class="badge {{ str_contains(strtolower($mvt->type), 'entr') ? 'in' : 'out' }}">
                        {{ $mvt->type }}
                    </span>
                </td>
                <td class="qty">
                    {{ str_contains(strtolower($mvt->type), 'entr') ? '+' : '-' }}{{ $mvt->quantity }}
                </td>
                <td>{{ $mvt->reason }}</td>
                <td>{{ $mvt->user->name ?? 'Système' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>