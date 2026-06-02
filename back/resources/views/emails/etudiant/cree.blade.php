<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .section {
            margin-top: 20px;
        }
        .identifiants {
            background-color: #f0f4ff;
            padding: 15px;
            border-left: 4px solid #4F46E5;
            border-radius: 4px;
        }
        .modules {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 4px;
        }
        .module-item {
            padding: 5px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur 2IOnline votre plateforme d'apprentissage en ligne </h1>
        </div>

        <div class="section">
            <p>Bonjour <strong>{{ $etudiant->user->prenom }} {{ $etudiant->user->nom }}</strong>,</p>
            <p>Votre compte etudiant(e) a été créé(e) avec succès. Voici vos informations de connexion :</p>
        </div>

        <div class="section identifiants">
            <h3>🔐 Vos identifiants</h3>
            <p><strong>Email :</strong> {{ $etudiant->user->email }}</p>
            <p><strong>Mot de passe temporaire :</strong> {{ $password }}</p>
            <p style="color: red; font-size: 13px;">
                ⚠️ Veuillez changer votre mot de passe après votre première connexion.
            </p>
        </div>



        <div class="section modules">
            <h3>📦 Formations que vous avez inscrit</h3>
            @forelse($etudiant->formations as $formation)
                <div class="module-item">
                    ✅ {{ $formation->titre }}
                </div>
            @empty
                <p>Aucune formation inscrit pour le moment.</p>
            @endforelse
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement par 2IOnline. Ne pas répondre.</p>
        </div>
    </div>
</body>
</html>
