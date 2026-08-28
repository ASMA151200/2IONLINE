<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; text-align: center; padding: 60px; background-color: #080F1E; color: #ffffff; }
        .border-box { border: 3px solid #C9A227; padding: 50px; height: 100%; }
        .logo { font-size: 24px; font-weight: bold; color: #C9A227; margin-bottom: 10px; }
        h1 { font-size: 20px; letter-spacing: 3px; text-transform: uppercase; color: #C9A227; margin-bottom: 30px; }
        .nom { font-size: 30px; font-weight: bold; margin: 15px 0; }
        .session { font-size: 16px; margin: 15px 0; color: #d0daf0; }
        .footer { margin-top: 50px; font-size: 11px; color: #999999; }
        .numero { margin-top: 15px; font-size: 10px; color: #777777; }
    </style>
</head>
<body>
    <div class="border-box">
        <div class="logo">2I ONLINE</div>
        <h1>Attestation de présence</h1>
        <p>Cette attestation certifie que</p>
        <div class="nom">{{ $user->prenom }} {{ $user->nom }}</div>
        <p>a assisté à la session en direct</p>
        <div class="session">{{ $liveSession->title }}</div>
        <div class="footer">Délivrée le {{ $date }}</div>
        <div class="numero">N° {{ $numero }}</div>
    </div>
</body>
</html>
