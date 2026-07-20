<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réinitialisation du mot de passe</title>
</head>
<body style="margin:0;padding:0;background:#ffffff;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;padding:32px 0;">
<tr>
<td align="center">

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.4);">

  <!-- Corps de l'email -->
  <tr>
    <td style="padding:40px 36px;font-family:Arial,Helvetica,sans-serif;color:#222;">

      <!-- Header -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td align="center" style="padding-bottom:28px;border-bottom:1px solid #f0f0f0;">
            <div style="font-size:22px;font-weight:700;color:#1b3a6b;letter-spacing:1px;margin-bottom:4px;">2i Online</div>
            <div style="font-size:11px;color:#c9a227;letter-spacing:2px;text-transform:uppercase;">L'excellence hôtelière à votre portée</div>
          </td>
        </tr>
      </table>

      <!-- Objet -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
        <tr>
          <td style="background:#f9f5e8;border-left:4px solid #c9a227;padding:12px 16px;border-radius:0 8px 8px 0;">
            <div style="font-size:11px;color:#999;margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:1px;">Objet</div>
            <div style="font-size:14px;color:#1b3a6b;font-weight:700;">Réinitialisation de votre mot de passe</div>
          </td>
        </tr>
      </table>

      <!-- Salutation -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
        <tr>
          <td>
            <p style="font-size:15px;color:#333;line-height:1.7;margin:0 0 14px;">Bonjour <strong>{{ $prenom }}</strong>,</p>
            <p style="font-size:14px;color:#555;line-height:1.8;margin:0;">Vous avez demandé la réinitialisation de votre mot de passe. Voici votre code de vérification :</p>
          </td>
        </tr>
      </table>

      <!-- Code -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;border-radius:12px;margin-top:28px;">
        <tr>
          <td align="center" style="padding:24px;">
            <div style="font-size:36px;font-weight:700;color:#1b3a6b;letter-spacing:8px;">{{ $code }}</div>
          </td>
        </tr>
      </table>

      <!-- Infos -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
        <tr>
          <td align="center">
            <p style="font-size:13px;color:#777;margin:0 0 10px;">Ce code expire dans <strong style="color:#1b3a6b;">5 minutes</strong>.</p>
            <p style="font-size:13px;color:#c0392b;margin:0;">⚠️ Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet e-mail.</p>
          </td>
        </tr>
      </table>

      <!-- Signature -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;border-top:1px solid #f0f0f0;">
        <tr>
          <td style="padding-top:24px;">
            <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 8px;">Pour toute question, répondez simplement à cet e-mail — notre équipe pédagogique est là pour vous.</p>
            <div style="font-size:14px;font-weight:700;color:#1b3a6b;">Bien culinairement,</div>
            <div style="font-size:13px;color:#c9a227;font-weight:600;margin-top:2px;">L'équipe 2i Online</div>
            <div style="font-size:11px;color:#aaa;margin-top:4px;font-style:italic;">L'excellence hôtelière à votre portée.</div>
          </td>
        </tr>
      </table>

    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td style="background:#f5f5f5;padding:16px 36px;text-align:center;border-top:1px solid #e0e0e0;">
      <p style="font-size:11px;color:#aaa;margin:0;"> Copyright © 2026 2i Online — Incub Institut, Bargny, Sénégal · Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</p>
    </td>
  </tr>

</table>

</td>
</tr>
</table>

</body>
</html>
