<?php

namespace App\Support;

/**
 * Nettoyage HTML basique sans dépendance externe (aucun purificateur type
 * HTMLPurifier n'est installé, et composer n'est pas exécutable depuis
 * cet environnement). Moins robuste qu'une vraie librairie dédiée, mais
 * réduit significativement la surface d'attaque XSS pour du contenu
 * riche saisi par un admin/formateur (actus.contenu_html).
 *
 * Note de contexte : ce contenu n'est actuellement modifiable QUE par un
 * compte admin/formateur (jamais un visiteur public), donc le risque
 * réel ne se matérialise qu'en cas de compte admin compromis — mais
 * defense-in-depth justifie quand même ce filtrage.
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><ul><ol><li><a><blockquote><img>';

    public static function clean(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        // 1. Retire tout ce qui n'est pas dans la liste blanche de balises.
        $html = strip_tags($html, self::ALLOWED_TAGS);

        // 2. Retire tout attribut "on*" (onclick, onerror, onload...) —
        // strip_tags() ne touche pas aux attributs des balises autorisées.
        $html = preg_replace('/\s*on\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html);

        // 3. Neutralise les URLs "javascript:" dans href/src.
        $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1=$2#$2', $html);

        return $html;
    }
}
