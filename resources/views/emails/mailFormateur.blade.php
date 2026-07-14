
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
            <div style="font-size:14px;color:#1b3a6b;font-weight:700;">Bienvenue chez 2i Online – Votre espace formateur est prêt !</div>
          </td>
        </tr>
      </table>

      <!-- Salutation -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
        <tr>
          <td>
            <p style="font-size:15px;color:#333;line-height:1.7;margin:0 0 14px;">Bonjour <strong>{{ $formateur->user->prenom }} {{ $formateur->user->nom }}</strong>,</p>
            <p style="font-size:14px;color:#555;line-height:1.8;margin:0 0 14px;">C'est avec grand plaisir que nous vous accueillons au sein de l'équipe pédagogique de <strong>2i Online</strong>.</p>
            <p style="font-size:14px;color:#555;line-height:1.8;margin:0 0 14px;">Votre expertise va permettre à nos étudiants de développer les compétences essentielles des métiers de l'hôtellerie et de la restauration.</p>
            <p style="font-size:14px;color:#555;line-height:1.8;margin:0;">Votre espace formateur est désormais actif : vous pouvez dès à présent consulter vos formations et préparer vos premiers contenus.</p>
          </td>
        </tr>
      </table>

      <!-- Accès -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;border-radius:12px;margin-top:28px;">
        <tr>
          <td style="padding:24px;">
            <div style="font-size:13px;font-weight:700;color:#1b3a6b;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;">🔐 Vos accès pour commencer dès maintenant</div>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#444;">
                  <span style="color:#c9a227;font-weight:700;display:inline-block;min-width:130px;">Espace Formateur :</span>
                  <a href="{{ $lien_plateforme ?? '#' }}" style="color:#1b3a6b;text-decoration:underline;">Accéder à la plateforme</a>
                </td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#444;">
                  <span style="color:#c9a227;font-weight:700;display:inline-block;min-width:130px;">Email :</span>
                  {{ $formateur->user->email }}
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
              <tr>
                <td style="padding:12px 0 0;font-size:13px;color:#444;">
                  <div style="color:#c9a227;font-weight:700;margin-bottom:6px;">Formation(s) à enseigner :</div>
                  @forelse($formateur->modules as $module)
                    <div style="padding:2px 0;">📘 {{ $module->titre }}</div>
                  @empty
                    <div style="font-style:italic;color:#777;">Aucun module attribué pour le moment.</div>
                  @endforelse
                </td>
              </tr>
            </table>

          </td>
        </tr>
      </table>

      <!-- CTA -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">
        <tr>
          <td align="center">
            <a href="{{ $lien_espace_formateur ?? '#' }}" style="display:inline-block;background:#c9a227;color:#1b3a6b;font-weight:700;font-size:14px;padding:14px 36px;border-radius:8px;text-decoration:none;letter-spacing:0.5px;">🎓 Accéder à mon espace formateur</a>
          </td>
        </tr>
      </table>

      <!-- Ce qui vous attend -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">
        <tr>
          <td style="font-size:13px;font-weight:700;color:#1b3a6b;padding-bottom:16px;text-transform:uppercase;letter-spacing:1px;">📅 Ce qui vous attend dans les prochains jours</td>
        </tr>
        <tr>
          <td style="padding-bottom:12px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
              <tr>
                <td width="32" valign="top" style="font-size:16px;">🎥</td>
                <td style="padding-left:12px;">
                  <div style="font-size:13px;font-weight:600;color:#222;margin-bottom:2px;">Préparation des contenus</div>
                  <div style="font-size:12px;color:#777;line-height:1.6;">Déposez vos supports de cours et vidéos depuis votre tableau de bord.</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="padding-bottom:12px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
              <tr>
                <td width="32" valign="top" style="font-size:16px;">🗓️</td>
                <td style="padding-left:12px;">
                  <div style="font-size:13px;font-weight:600;color:#222;margin-bottom:2px;">Planification</div>
                  <div style="font-size:12px;color:#777;line-height:1.6;">Définissez vos créneaux de classe virtuelle et vos sessions de suivi.</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table role="presentation" cellpadding="0" cellspacing="0">
              <tr>
                <td width="32" valign="top" style="font-size:16px;">🖥️</td>
                <td style="padding-left:12px;">
                  <div style="font-size:13px;font-weight:600;color:#222;margin-bottom:2px;">Prise en main</div>
                  <div style="font-size:12px;color:#777;line-height:1.6;">Un membre de notre équipe pédagogique vous contactera pour une session d'onboarding.</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

      <!-- Signature -->
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;border-top:1px solid #f0f0f0;">
        <tr>
          <td style="padding-top:24px;">
            <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 8px;">Nous sommes ravis de vous compter parmi nos formateurs. Pour toute question, répondez simplement à cet e-mail — notre équipe pédagogique est là pour vous.</p>
            <p style="font-size:13px;color:#555;margin:0 0 16px;">Au plaisir de collaborer avec vous !</p>
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
      <p style="font-size:11px;color:#aaa;margin:0;"> Copyright © 2026 2i Online — Incub Institut, Bargny, Sénégal · <a href="{{ $lien_desabonnement ?? '#' }}" style="color:#c9a227;text-decoration:none;">Se désabonner</a></p>
    </td>
  </tr>

</table>

</td>
</tr>
</table>

</body>
</html>






