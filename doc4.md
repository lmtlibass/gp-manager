# Documentation du projet GP Manager

## Présentation générale
GP Manager est une application web de gestion logistique de colis pour des agences de transport ou des points relais.
L'application permet de suivre le cycle de vie des colis, gérer les clients expéditeurs/récepteurs, traiter les retraits en agence, et centraliser les notifications de livraison.

## Idée de l'application
Le projet vise à offrir un tableau de bord et des interfaces métier pour :
- suivre l'état des colis depuis la réception jusqu'à la livraison,
- gérer les clients (expéditeurs et récepteurs),
- gérer les agences et le personnel,
- traiter le retrait final des colis,
- gérer l'envoi et le suivi des notifications liées aux colis.

## Utilisateurs et rôles
L'application gère plusieurs types de rôles :
- `admin` : accès total, gestion des utilisateurs et des agences,
- `gp_manager` : manager d'agence, peut gérer les colis, clients, notifications, retraits et utilisateurs de son agence,
- `agent` : profil opérationnel, peut consulter et mettre à jour les colis, traiter les retraits et consulter les notifications.

## Fonctionnalités principales

### 1. Authentification et gestion des comptes
- pages de connexion, mot de passe oublié et réinitialisation,
- middleware `active` qui bloque et déconnecte les utilisateurs désactivés,
- profil utilisateur avec modification des informations de base.

### 2. Dashboard
- vue synthétique des colis par statut : reçus, en transit, arrivés, prêts à être retirés, livrés,
- filtres sur les colis par agence ou par utilisateur créateur,
- affichage des derniers colis et des dernières mises à jour de statut.

### 3. Gestion des colis
- création et modification de colis,
- génération automatique de numéros de suivi et de codes de retrait,
- saisie des détails : expéditeur, destinataire, poids, valeur déclarée, pays d'origine et destination,
- mise à jour des statuts de colis et historique des changements de statut,
- recherche et tri des colis par numéro de suivi, code retrait, expéditeur, destinataire ou statut.

### 4. Gestion des clients
- gestion des clients expéditeurs et récepteurs,
- visibilité par agence (sauf pour l'admin qui voit tout),
- champs de contact complets et informations d'adresse.

### 5. Retrait des colis
- interface de recherche par code de retrait,
- validation du retrait avec saisie du nom du destinataire, téléphone, pièce d'identité et notes,
- création d'un enregistrement de retrait,
- passage automatique du colis au statut `livré` lors du retrait.

### 6. Notifications
- saisie d'une notification liée à un colis,
- support de différents canaux de notification (par exemple WhatsApp ou SMS),
- suivi du statut de la notification : `pending`, `sent`, `failed`,
- possibilité de marquer manuellement une notification comme envoyée ou échouée.

### 7. Gestion des utilisateurs
- création et modification des comptes utilisateurs,
- activation / désactivation des comptes,
- filtrage par rôle et agence,
- droits de gestion d'utilisateurs réservés aux administrateurs et aux managers.

## Architecture et stack technologique

### Backend
- Laravel 13.x
- PHP 8.3
- Laravel Fortify pour l'authentification
- Eloquent ORM pour les modèles métier
- Livewire 4.x pour les composants réactifs côté serveur
- Policies et Gates pour l'autorisation métier

### Frontend
- Blade templates Laravel
- Vite pour le bundling
- Tailwind CSS via `@tailwindcss/vite`
- Livewire pour les composants d'interface dynamique

### Modèles de données clés
- `Package` : gestion des colis, statuts, agences, expéditeur, destinataire,
- `Customer` : expéditeur/receveur,
- `Withdrawal` : retrait de colis,
- `PackageNotification` : notifications liées aux colis,
- `User` : utilisateurs, rôles et appartenance à une agence,
- `Agency` : agences logistiques.

## Flux métier principal
1. Un utilisateur authentifié accède au dashboard.
2. Il crée ou édite un colis en sélectionnant l'expéditeur et le destinataire.
3. Le colis reçoit un numéro de suivi et un code de retrait.
4. Le statut du colis évolue dans le temps (`reçu`, `en transit`, `arrivé`, `prêt pour retrait`, `livré`).
5. Un agent de retrait saisit le code de retrait, valide le retrait et marque le colis comme livré.
6. L'équipe peut émettre et suivre des notifications liées à un colis.

## Points de sécurité et contrôle d'accès
- Middleware `auth` + `active` pour restreindre l'accès aux pages.
- Autorisations basées sur les rôles et l'agence de l'utilisateur.
- `UserPolicy`, `PackagePolicy`, `CustomerPolicy`, `WithdrawalPolicy` pour protéger l'accès aux ressources.
- Validation des données côté serveur et règles métier sur les formulaires Livewire.

## Notes spécifiques
- Le projet est structuré comme une application Laravel classique avec un fort usage de Livewire pour les interfaces interactives.
- Il est conçu pour être multi-agence avec des droits de visibilité et de gestion restreints par agence.
- Les notifications sont gérées en base de données, mais le code ne montre pas de service d'envoi extérieur intégré ; il s'agit d'un suivi interne des notifications.

---

Cette documentation est un résumé du fonctionnement et de la stack du projet GP Manager. Si tu veux, je peux aussi générer un diagramme des flux ou une documentation plus détaillée par fonctionnalité.`}