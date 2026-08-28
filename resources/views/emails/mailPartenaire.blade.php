<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenue chez 2i Online</title>
</head>
<body style="margin:0;padding:0;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;padding:32px 0;">
<tr>
<td align="center">

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.4);">

  <tr>
    <td style="padding:40px 36px;font-family:Arial,Helvetica,sans-serif;color:#222;">

      <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td align="center" style="padding-bottom:28px;border-bottom:1px solid #f0f0f0;">
            <div style="font-size:22px;font-weight:700;color:#1b3a6b;letter-spacing:1px;margin-bottom:4px;">2i Online</div>
            <div style="font-size:11px;color:#c9a227;letter-spacing:2px;text-transform:uppercase;">L'excellence hôtelière à votre portée</div>
          </td>
        </tr>
      </table>

      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
        <tr>
          <td style="background:#f9f5e8;border-left:4px solid #c9a227;padding:12px 16px;border-radius:0 8px 8px 0;">
            <div style="font-size:11px;color:#999;margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:1px;">Objet</div>
            <div style="font-size:14px;color:#1b3a6b;font-weight:700;">Bienvenue chez 2i Online – Votre espace partenaire est prêt !</div>
          </td>
        </tr>
      </table>

      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
        <tr>
          <td>
            <p style="font-size:15px;color:#333;line-height:1.7;margin:0 0 14px;">Bonjour <strong>{{ $partenaire->user->prenom }} {{ $partenaire->user->nom }}</strong>,</p>
            <p style="font-size:14px;color:#555;line-height:1.8;margin:0 0 14px;">C'est avec grand plaisir que nous accueillons <strong>{{ $partenaire->nom_organisation }}</strong> parmi les partenaires de <strong>2i Online</strong>.</p>
            <p style="font-size:14px;color:#555;line-height:1.8;margin:0;">Votre espace partenaire est désormais actif : vous pouvez suivre l'évolution des formations que vous financez, la progression des étudiants et l'impact de votre soutien.</p>
          </td>
        </tr>
      </table>

      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;border-radius:12px;margin-top:28px;">
        <tr>
          <td style="padding:24px;">
            <div style="font-size:13px;font-weight:700;color:#1b3a6b;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;">🔐 Vos accès pour commencer dès maintenant</div>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#444;">
                  <span style="color:#c9a227;font-weight:700;display:inline-block;min-width:130px;">Espace Partenaire :</span>
                  <a href="{{ $lien_plateforme ?? '#' }}" style="color:#1b3a6b;text-decoration:underline;">Accéder à la plateforme</a>
                </td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#444;">
                  <span style="color:#c9a227;font-weight:700;display:inline-block;min-width:130px;">Email :</span>
                  {{ $partenaire->user->email }}
                </td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#444;">
                  <span style="color:#c9a227;font-weight:700;display:inline-block;min-width:130px;">Mot de passe :</span>
                  {{ $password }}
                </td>
              </tr>
              <tr>
                <td style="padding:8px 0 4px;font-size:13px;color:#c0392b;">
                  ⚠️ Veuillez changer votre mot de passe après votre première connexion.
                </td>
              </tr>
            </table>

          </td>
        </tr>
      </table>

      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">
        <tr>
          <td align="center">
            <a href="{{ $lien_espace_partenaire ?? '#' }}" style="display:inline-block;background:#c9a227;color:#1b3a6b;font-weight:700;font-size:14px;padding:14px 36px;border-radius:8px;text-decoration:none;letter-spacing:0.5px;">🤝 Accéder à mon espace partenaire</a>
          </td>
        </tr>
      </table>

      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;border-top:1px solid #f0f0f0;">
        <tr>
          <td style="padding-top:24px;">
            <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 8px;">Nous sommes ravis de compter votre organisation parmi nos partenaires. Pour toute question, répondez simplement à cet e-mail — notre équipe est là pour vous.</p>
            <p style="font-size:13px;color:#555;margin:0 0 16px;">Merci pour votre engagement !</p>
            <div style="font-size:14px;font-weight:700;color:#1b3a6b;">Bien cordialement,</div>
            <div style="font-size:13px;color:#c9a227;font-weight:600;margin-top:2px;">L'équipe 2i Online</div>
          </td>
        </tr>
      </table>

    </td>
  </tr>

  <tr>
    <td style="background:#f5f5f5;padding:16px 36px;text-align:center;border-top:1px solid #e0e0e0;">
      <p style="font-size:11px;color:#aaa;margin:0;"> Copyright © 2026 2i Online — Incub Institut, Bargny, Sénégal</p>
    </td>
  </tr>

</table>

</td>
</tr>
</table>

</body>
</html>
