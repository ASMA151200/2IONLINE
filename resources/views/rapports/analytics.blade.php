<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1a1a2e; padding: 30px; }
        h1 { color: #C9A227; font-size: 22px; border-bottom: 2px solid #C9A227; padding-bottom: 10px; }
        h2 { color: #0D2545; font-size: 16px; margin-top: 25px; }
        .meta { color: #666; font-size: 11px; margin-bottom: 20px; }
        .stat-grid { display: table; width: 100%; margin: 15px 0; }
        .stat-cell { display: table-cell; width: 16.6%; padding: 10px 5px; text-align: center; border: 1px solid #eee; }
        .stat-value { font-size: 18px; font-weight: bold; color: #C9A227; }
        .stat-label { font-size: 9px; color: #666; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #0D2545; color: white; }
        tr:nth-child(even) { background: #f7f7f9; }
        .footer { margin-top: 40px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>2I Online — Rapport analytique</h1>
    <p class="meta">Période : {{ $periode }} — Généré le {{ $genereLe }}</p>

    <h2>Vue d'ensemble</h2>
    <div class="stat-grid">
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['totalStudents'] }}</div>
            <div class="stat-label">Apprenants</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ number_format($stats['totalRevenue'], 0, ',', ' ') }}</div>
            <div class="stat-label">Revenus (FCFA)</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['totalEnrollments'] }}</div>
            <div class="stat-label">Inscriptions</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['activeUsers'] }}</div>
            <div class="stat-label">Comptes actifs</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['completionRate'] }}%</div>
            <div class="stat-label">Taux de réussite</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['averageScore'] }}</div>
            <div class="stat-label">Score moyen</div>
        </div>
    </div>

    <h2>Détail par formation</h2>
    <table>
        <thead>
            <tr>
                <th>Formation</th>
                <th>Inscrits</th>
                <th>Terminés</th>
                <th>Taux de réussite</th>
                <th>Revenus (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($formations as $f)
                <tr>
                    <td>{{ $f['name'] }}</td>
                    <td>{{ $f['enrolledStudents'] }}</td>
                    <td>{{ $f['completedStudents'] }}</td>
                    <td>{{ $f['completionRate'] }}%</td>
                    <td>{{ number_format($f['revenue'], 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune donnée pour cette période.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Document généré automatiquement par 2I Online — usage interne et partage autorisé avec les partenaires.
    </div>
</body>
</html>
