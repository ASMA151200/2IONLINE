<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            padding: 60px;
            background-color: #080F1E;
            color: #ffffff;
        }
        .border-box {
            border: 3px solid #C9A227;
            padding: 50px;
            height: 100%;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #C9A227;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 22px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #C9A227;
            margin-bottom: 40px;
        }
        .nom {
            font-size: 36px;
            font-weight: bold;
            margin: 20px 0;
        }
        .formation {
            font-size: 20px;
            margin: 20px 0;
            color: #d0daf0;
        }
        .footer {
            margin-top: 60px;
            font-size: 12px;
            color: #999999;
        }
        .numero {
            margin-top: 20px;
            font-size: 11px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="border-box">
        <div class="logo">2I ONLINE</div>
        <h1>Certificat de réussite</h1>

        <p>Ce certificat est décerné à</p>
        <div class="nom">{{ $user->prenom }} {{ $user->nom }}</div>

        <p>pour avoir suivi avec succès la formation</p>
        <div class="formation">{{ $formation->titre }}</div>

        <div class="footer">
            Délivré le {{ $date }}
        </div>
        <div class="numero">
            N° {{ $numero }}
        </div>
    </div>
</body>
</html>
