<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1a1a2e; padding: 30px; }
        h1 { color: #C9A227; font-size: 22px; border-bottom: 2px solid #C9A227; padding-bottom: 10px; }
        h2 { color: #0D2545; font-size: 16px; margin-top: 25px; }
        .stat-grid { display: table; width: 100%; margin: 15px 0; }
        .stat-cell { display: table-cell; width: 25%; padding: 10px; text-align: center; border: 1px solid #eee; }
        .stat-value { font-size: 20px; font-weight: bold; color: #C9A227; }
        .stat-label { font-size: 10px; color: #666; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #0D2545; color: white; }
        .footer { margin-top: 40px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>Rapport d'impact — {{ $partenaire->nom_organisation }}</h1>
    <p>Généré le {{ $date }}</p>

    <div class="stat-grid">
        <div class="stat-cell">
            <div class="stat-value">{{ number_format($stats['totalInvesti'], 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Total investi</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['totalEtudiants'] }}</div>
            <div class="stat-label">Étudiants soutenus</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['tauxReussite'] }}%</div>
            <div class="stat-label">Taux de réussite</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['certificatsDelivres'] }}</div>
            <div class="stat-label">Certificats délivrés</div>
        </div>
    </div>

    <h2>Formations financées</h2>
    <table>
        <thead>
            <tr><th>Formation</th><th>Montant financé</th><th>Date</th><th>Inscrits</th></tr>
        </thead>
        <tbody>
            @foreach($formations as $f)
            <tr>
                <td>{{ $f->titre }}</td>
                <td>{{ number_format($f->pivot->montant_finance, 0, ',', ' ') }} FCFA</td>
                <td>{{ \Carbon\Carbon::parse($f->pivot->date_financement)->format('d/m/Y') }}</td>
                <td>{{ $f->inscriptions_count ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">2I Online — Incub Institut, Bargny, Sénégal</div>
</body>
</html>
