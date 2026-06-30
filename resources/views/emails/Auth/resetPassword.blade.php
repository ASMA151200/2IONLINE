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
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #4F46E5;
            text-align: center;
            letter-spacing: 8px;
            padding: 20px;
            background-color: #f0f4ff;
            border-radius: 8px;
            margin: 20px 0;
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
            <h1>Réinitialisation du mot de passe</h1>
        </div>

        <div class="section">
            <p>Bonjour <strong>{{ $prenom }}</strong>,</p>
            <p>Vous avez demandé la réinitialisation de votre mot de passe. Voici votre code :</p>
        </div>

        <div class="code">{{ $code }}</div>

        <p style="text-align: center; color: #6b7280;">Ce code expire dans <strong>5 minutes</strong>.</p>
        <p style="color: red; text-align: center;">Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement par 2IOnline. Ne pas répondre.</p>
        </div>
    </div>
</body>
</html>
