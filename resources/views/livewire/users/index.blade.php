<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <p class="page-eyebrow">Administration</p>
                <h1 class="page-heading mt-1">Utilisateurs</h1>
                <p class="page-subtitle">Comptes, rôles et accès par agence.</p>
            </div>
            @can('create', App\Models\User::class)
            <button wire:click="create" class="btn-primary">
                <i class="fa-solid fa-plus"></i>Nouvel utilisateur
            </button>
            @endcan
        </div>
    </div>

    {{-- Form panel --}}
    @if ($showForm)
    <div class="card-panel">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h2 class="section-title">{{ $editingUserId ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Remplissez les informations du compte.</p>
            </div>
            <button wire:click="$set('showForm', false)"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form wire:submit="save" class="space-y-5">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="form-label">Prénom</label>
                    <input wire:model="first_name" type="text" class="form-input" placeholder="Jean">
                    @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nom</label>
                    <input wire:model="last_name" type="text" class="form-input" placeholder="Dupont">
                    @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input wire:model="email" type="email" class="form-input" placeholder="jean@exemple.com">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Téléphone</label>
                    <input wire:model="phone" type="tel" class="form-input" placeholder="+33 6 00 00 00 00">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Mot de passe</label>
                    <input wire:model="password" type="password" class="form-input" placeholder="{{ $editingUserId ? 'Laisser vide pour ne pas changer' : '••••••••' }}">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Confirmation</label>
                    <input wire:model="password_confirmation" type="password" class="form-input" placeholder="••••••••">
                </div>
                <div>
                    <label class="form-label">Rôle</label>
                    <select wire:model="role" class="form-select">
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Agence</label>
                    <select wire:model="agency_id" class="form-select">
                        <option value="">Choisir une agence</option>
                        @foreach ($agencies as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                        @endforeach
                    </select>
                    @error('agency_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <input wire:model="is_active" type="checkbox" id="is_active_form"
                        class="h-4 w-4 rounded border-slate-300 text-[#0062a3] focus:ring-[#0062a3]">
                    <label for="is_active_form" class="text-sm font-medium text-slate-700 cursor-pointer">
                        Compte actif
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <button type="button" wire:click="$set('showForm', false)" class="btn-secondary">Annuler</button>
                <button type="submit" wire:loading.attr="disabled" class="btn-primary">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-floppy-disk"></i>Enregistrer
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>Enregistrement…
                    </span>
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Table card --}}
    <div class="card-panel space-y-0">

        {{-- Filters --}}
        <div class="grid gap-3 border-b border-slate-100 pb-5 md:grid-cols-5">
            <div class="relative md:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Nom, email, téléphone…"
                    class="form-input pl-10">
            </div>
            <select wire:model.live="roleFilter" class="form-select">
                <option value="">Tous les rôles</option>
                @foreach ($roleFilters as $roleOption)
                    <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="statusFilter" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="active">Actifs</option>
                <option value="inactive">Inactifs</option>
            </select>
            @if ($canFilterAgency)
            <select wire:model.live="agencyFilter" class="form-select">
                <option value="">Toutes agences</option>
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                @endforeach
            </select>
            @else
            <select wire:model.live="perPage" class="form-select">
                <option value="10">10 par page</option>
                <option value="25">25 par page</option>
                <option value="50">50 par page</option>
            </select>
            @endif
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto">
            <table class="table-surface w-full">
                <thead>
                    <tr>
                        <th class="table-header">
                            <button wire:click="sortBy('first_name')" class="flex items-center gap-1 hover:text-white">
                                Utilisateur <i class="fa-solid fa-sort text-[10px] opacity-60"></i>
                            </button>
                        </th>
                        <th class="table-header">
                            <button wire:click="sortBy('role')" class="flex items-center gap-1 hover:text-white">
                                Rôle <i class="fa-solid fa-sort text-[10px] opacity-60"></i>
                            </button>
                        </th>
                        <th class="table-header">Agence</th>
                        <th class="table-header">
                            <button wire:click="sortBy('is_active')" class="flex items-center gap-1 hover:text-white">
                                Statut <i class="fa-solid fa-sort text-[10px] opacity-60"></i>
                            </button>
                        </th>
                        <th class="table-header text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="table-row-hover">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#001F3D] text-xs font-bold uppercase text-white">
                                    {{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $user->full_name }}</div>
                                    <div class="text-xs text-slate-400">{{ $user->email }} · {{ $user->phone ?: 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $user->role->badgeClasses() }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">{{ $user->agency->name ?? '—' }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset
                                {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-500/20' : 'bg-rose-50 text-rose-700 ring-rose-500/20' }}">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            @can('update', $user)
                            <button wire:click="edit({{ $user->id }})"
                                class="font-semibold text-[#0062a3] hover:text-[#004e7b] transition-colors">
                                Modifier
                            </button>
                            <button wire:click="toggleActive({{ $user->id }})"
                                class="ml-4 font-semibold text-slate-500 hover:text-slate-700 transition-colors">
                                {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                            @endcan
                            @can('delete', $user)
                            <button wire:click="delete({{ $user->id }})"
                                wire:confirm="Supprimer cet utilisateur ?"
                                class="ml-4 font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                Supprimer
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-14 text-center">
                            <i class="fa-solid fa-users text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">Aucun utilisateur trouvé.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-slate-100 pt-4">
            {{ $users->links() }}
        </div>

    </div>
</div>
