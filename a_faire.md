Audit GP Manager — Rapport Complet
Résumé des problèmes critiques
Sévérité	Problème	Fichier	Impact
CRITIQUE	Password non hashé lors création user	Users/Index.php	Mots de passe en clair en DB
CRITIQUE	UserController bypass toutes les Policies	UserController.php	Suppression/édition non autorisée
CRITIQUE	index() sans filtre agency pour GpManager	UserController.php:44	Data leak entre agences
MAJEUR	Withdrawal code : 9000 combinaisons seulement	Package.php	Collision probable, énumération possible
MAJEUR	Race condition sur le retrait	Withdrawals/Process.php	Retraits en double
MAJEUR	N+1 queries sur le Dashboard	Dashboard.php	Performance critique
IMPORTANT	Pas de validation longueur sur search	Packages/Index.php	DoS via LIKE massif
IMPORTANT	Index manquant sur agency_id (customers)	migration	Requêtes lentes
1. Sécurité
1.1 Password non hashé — CRITIQUE
Dans Users/Index.php, le password n'est pas explicitement hashé avant User::create(). Même si le cast hashed existe dans le modèle, ce comportement implicite est fragile.


// PROBLÈME — actuel
User::query()->create($data);

// CORRECTION
$data['password'] = Hash::make($data['password']);
User::query()->create($data);
1.2 UserController bypass les Policies — CRITIQUE
UserController.php n'appelle $this->authorize() nulle part. Les méthodes index, update, destroy sont entièrement exposées à n'importe quel utilisateur authentifié :


// PROBLÈME — destroy() sans vérification
public function destroy(User $user)
{
    $user->delete(); // N'importe qui peut supprimer n'importe quel user
}

// CORRECTION
public function destroy(User $user)
{
    $this->authorize('delete', $user);
    $user->delete();
}
Même problème sur index() : un GpManager voit tous les utilisateurs de toutes les agences.

1.3 Withdrawal Code trop court — MAJEUR
Dans Package.php :


$code = (string) random_int(1000, 9999); // Seulement 9 000 combinaisons
À ~1 000 colis actifs, probabilité de collision > 50% (paradoxe des anniversaires). De plus, la boucle est infinie si tous les codes sont pris.


// CORRECTION
$code = strtoupper(Str::random(6)); // 2 milliards de combinaisons
1.4 Race condition sur le retrait — MAJEUR
Dans Withdrawals/Process.php, la vérification et la création sont séparées, permettant deux retraits simultanés :


// CORRECTION — vérifier DANS la transaction
DB::transaction(function (): void {
    if ($this->package->fresh()->withdrawal) {
        throw new LogicException('Colis déjà retiré');
    }
    Withdrawal::query()->create([...]);
    $this->package->update(['status' => PackageStatus::Delivered]);
});
1.5 Pas de rate limiting sur le login
Aucun throttle sur /login — attaque brute-force possible.


// routes/web.php
Route::post('/login', [LoginController::class, 'login'])
    ->middleware(['throttle:5,1'])
    ->name('login.store');
1.6 Pas de security headers HTTP
Aucun header X-Frame-Options, X-Content-Type-Options, Referrer-Policy n'est configuré.

2. Architecture & Qualité du code
2.1 Mélange Controller classique / Livewire — incohérence
Routes en double pour la gestion des users : une vers user.index (Blade classique via Controller), une autre vers le composant Livewire Users/Index.php. Le Controller ne vérifie pas les permissions et ne filtre pas par agence.

Recommandation : Standardiser tout le CRUD sur Livewire, supprimer les routes Controller qui ne font que doublon.

2.2 N+1 Queries — MAJEUR
Dans Dashboard.php, 5 requêtes COUNT distinctes au lieu d'une seule :


// PROBLÈME — 5 COUNT séparés
'total' => (clone $query)->count(),
'received' => (clone $query)->where('status', ...)->count(),
// ...

// CORRECTION — une seule requête
$counts = $query->selectRaw('status, count(*) as count')
    ->groupBy('status')
    ->pluck('count', 'status');
Dans Packages/Index.php, les histories des packages ne sont pas eager-loadées :


// CORRECTION
->with(['sender', 'receiver', 'agency', 'histories.user'])
2.3 Duplication de logique de visibilité
Package::visibleFor($user) est répliqué dans Dashboard, GlobalSearch, PackagesIndex, etc. Créer un PackageRepository élimine cette duplication.

3. Modèles & Base de données
3.1 Index manquants
customers.agency_id — utilisé dans tous les scopes visibleFor(), pas indexé après la migration 2026_05_11
packages.withdrawal_code — recherché dans Withdrawals/Process.php, pas indexé

// Dans les migrations
$table->index('agency_id');
$table->index('withdrawal_code');
3.2 Relation Agency → Customer manquante
Après la migration add_agency_id_to_customers, la relation hasMany n'a pas été ajoutée dans le modèle Agency.

3.3 Pas d'audit trail complet
Seuls les Package ont un historique (PackageHistory). Les modifications de User et Customer (ex. changement de téléphone, email) ne sont pas tracées.

4. Logique métier
4.1 Pas de State Machine pour les transitions de statut
N'importe quel utilisateur avec les droits peut faire passer un colis de received directement à delivered sans passer par les étapes intermédiaires.


// Implémenter une transition guard
private const TRANSITIONS = [
    'received'        => ['in_transit'],
    'in_transit'      => ['arrived'],
    'arrived'         => ['ready_for_pickup'],
    'ready_for_pickup'=> ['delivered'],
    'delivered'       => [],
];
4.2 Notifications jamais réellement envoyées
Notifications/Index.php ne fait que stocker en base. Aucun appel à une API WhatsApp/SMS. Les méthodes markSent/markFailed changent juste le statut manuellement.

Recommandation : Implémenter une SendPackageNotificationJob dispatchée en queue.

4.3 Password en mémoire Livewire
Les champs $password et $password_confirmation comme propriétés publiques Livewire restent en session. Utiliser #[Locked] ou vider ces champs après traitement.

Plan d'action prioritaire
Phase 1 — Immédiat (sécurité bloquante)

Hash explicite du password dans Users/Index.php
Ajouter $this->authorize() dans toutes les méthodes de UserController.php
Agrandir l'espace du withdrawal_code (alphanumérique 6+ chars)
Correction de la race condition dans Withdrawals/Process.php
Phase 2 — Court terme

Indexes DB manquants (agency_id, withdrawal_code)
Rate limiting sur login
Validation max:50 sur les champs de recherche
Refactoring N+1 queries (Dashboard + PackagesIndex)
Phase 3 — Moyen terme

Standardiser sur Livewire, supprimer les doublons Controller
State Machine pour les transitions de statut
Security headers HTTP
Audit trail complet (Users, Customers)
Phase 4 — Long terme

Implémenter l'envoi réel des notifications (queue jobs)
2FA via Fortify
GDPR compliance (export, droit à l'oubli)
L'application a une bonne base architecturale (Policies, scopes, enums), mais les failles dans UserController et le hachage du mot de passe sont à corriger en priorité absolue. Veux-tu que je corrige directement ces points ?