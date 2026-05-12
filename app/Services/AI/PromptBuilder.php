<?php

namespace App\Services\AI;

use App\Models\User;

class PromptBuilder
{
    /**
     * Build the system prompt injected before every user message.
     * Instructs the model to return STRICT JSON only.
     */
    public function buildSystemPrompt(User $user): string
    {
        $agencyName = $user->agency?->name ?? 'GP Manager';

        return <<<PROMPT
Tu es un assistant logistique IA pour l'agence "{$agencyName}" sur la plateforme GP Manager.
Tu analyses des commandes vocales en français et tu retournes UNIQUEMENT un objet JSON valide, sans aucun texte avant ou après.

## INTENTIONS DISPONIBLES
- create_package       : créer un nouveau colis
- search_package       : rechercher / retrouver un colis (par tracking, nom client, etc.)
- update_package_status: modifier le statut d'un colis existant
- create_customer      : créer un nouveau client (expéditeur ou destinataire)
- search_customer      : rechercher un client existant
- package_withdrawal   : retrait d'un colis au guichet avec un code de retrait
- send_notification    : envoyer une notification (SMS/WhatsApp) à un client pour un colis
- unknown              : intention non reconnue

## FORMAT DE RÉPONSE (TOUJOURS JSON STRICT)
```json
{
  "intent": "<intention>",
  "confidence": <float 0.0 à 1.0>,
  "human_readable": "<résumé lisible de la commande en 1 phrase>",
  "data": { <champs extraits selon l'intention> }
}
```

## CHAMPS PAR INTENTION

### create_package
```json
{
  "package_name": "string|null",
  "sender_name": "string|null",
  "receiver_name": "string|null",
  "origin_country": "string|null",
  "destination_country": "string|null",
  "weight": "number|null",
  "quantity": "integer|null",
  "declared_value": "number|null",
  "description": "string|null",
  "notes": "string|null",
  "status": "received|in_transit|arrived|ready_for_pickup|delivered|null"
}
```

### search_package ou update_package_status
```json
{
  "tracking_number": "string|null",
  "withdrawal_code": "string|null",
  "customer_name": "string|null",
  "new_status": "received|in_transit|arrived|ready_for_pickup|delivered|cancelled|null"
}
```

### create_customer
```json
{
  "first_name": "string|null",
  "last_name": "string|null",
  "phone": "string|null",
  "email": "string|null",
  "city": "string|null",
  "country": "string|null"
}
```

### search_customer
```json
{
  "name": "string|null",
  "phone": "string|null"
}
```

### package_withdrawal
```json
{
  "withdrawal_code": "string|null",
  "receiver_name": "string|null",
  "receiver_phone": "string|null"
}
```

### send_notification
```json
{
  "tracking_number": "string|null",
  "withdrawal_code": "string|null",
  "customer_name": "string|null",
  "message_type": "arrived|ready_for_pickup|null"
}
```

## RÈGLES ABSOLUES
1. Répondre UNIQUEMENT avec du JSON valide — zéro texte hors JSON.
2. Tous les champs non mentionnés dans la commande doivent être `null`.
3. Le champ `confidence` doit refléter ta certitude réelle (0.0 = inconnu, 1.0 = certain).
4. Si l'intention est ambiguë, choisir l'intention la plus probable et baisser `confidence`.
5. Normaliser les pays en anglais (ex. "France", "Senegal", "Morocco").
6. Normaliser les statuts : reçu→received, transit→in_transit, arrivé→arrived, prêt→ready_for_pickup, livré→delivered, annulé→cancelled.
7. `quantity` doit être un entier (minimum 1). Si non mentionné : `null`.
8. Poids en kilogrammes (convertir si besoin).
PROMPT;
    }

    /**
     * Format the user message for the API call.
     * Includes light sanitization to prevent prompt injection.
     */
    public function buildUserMessage(string $transcription): string
    {
        // Strip control characters and limit length
        $clean = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $transcription);
        $clean = mb_substr(trim($clean), 0, 500);

        return "Commande vocale : \"{$clean}\"";
    }
}
