<?php

namespace App\Livewire\Users;

use App\Enums\UserRoleEnum;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $agencyFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public bool $showForm = false;
    public ?int $editingId = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'agent';
    public int|string|null $agency_id = null;
    public bool $is_active = true;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);

        if (request()->routeIs('users.create')) {
            $this->create();
        }

        if (request()->routeIs('users.edit') && request()->route('user')) {
            $this->edit((int) request()->route('user'));
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAgencyFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('create', User::class);
        $this->resetForm();

        $actor = Auth::user();
        $this->agency_id = $actor->role === UserRoleEnum::Admin ? Agency::query()->orderBy('name')->value('id') : $actor->agency_id;
        $this->role = UserRoleEnum::Agent->value;
        $this->showForm = true;
    }

    public function edit(int $userId): void
    {
        $user = $this->baseUserQuery()->findOrFail($userId);
        $this->authorize('update', $user);

        $this->editingId = $user->id;
        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role->value;
        $this->agency_id = $user->agency_id;
        $this->is_active = $user->is_active;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $target = $this->editingId ? $this->baseUserQuery()->findOrFail($this->editingId) : null;

        $this->authorize($target ? 'update' : 'create', $target ?: User::class);

        $data = $this->validate();
        $data = $this->enforceRoleRules($data);
        $data['name'] = trim($data['first_name'].' '.$data['last_name']);

        if ($target) {
            if ($data['password'] === '') {
                unset($data['password']);
            }

            unset($data['password_confirmation']);
            $target->update($data);
        } else {
            unset($data['password_confirmation']);
            User::query()->create($data);
        }

        $this->resetForm();
        session()->flash('success', 'Utilisateur enregistré.');
    }

    public function toggleActive(int $userId): void
    {
        $user = $this->baseUserQuery()->findOrFail($userId);
        $this->authorize('update', $user);

        $user->update(['is_active' => ! $user->is_active]);
    }

    public function delete(int $userId): void
    {
        $user = $this->baseUserQuery()->findOrFail($userId);
        $this->authorize('delete', $user);

        $user->delete();
        session()->flash('success', 'Utilisateur supprimé.');
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $actor = Auth::user();

        return view('livewire.users.index', [
            'users' => $this->baseUserQuery()
                ->with('agency')
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($query): void {
                        $query->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%")
                            ->orWhere('phone', 'like', "%{$this->search}%");
                    });
                })
                ->when($this->roleFilter !== '', fn ($query) => $query->where('role', $this->roleFilter))
                ->when($this->statusFilter !== '', fn ($query) => $query->where('is_active', $this->statusFilter === 'active'))
                ->when($actor->role === UserRoleEnum::Admin && $this->agencyFilter !== '', fn ($query) => $query->where('agency_id', $this->agencyFilter))
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'roles' => $this->availableRoles(),
            'roleFilters' => UserRoleEnum::cases(),
            'agencies' => $this->availableAgencies(),
            'canFilterAgency' => $actor->role === UserRoleEnum::Admin,
        ]);
    }

    protected function rules(): array
    {
        $allowedRoles = collect($this->availableRoles())->map->value->all();
        $allowedAgencies = $this->availableAgencies()->pluck('id')->all();
        $passwordRules = $this->editingId
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $passwordRules,
            'role' => ['required', Rule::in($allowedRoles)],
            'agency_id' => ['required', 'integer', Rule::in($allowedAgencies)],
            'is_active' => ['boolean'],
        ];
    }

    private function baseUserQuery()
    {
        $actor = Auth::user();

        return User::query()
            ->when($actor->role !== UserRoleEnum::Admin, fn ($query) => $query
                ->where('agency_id', $actor->agency_id)
                ->where('role', UserRoleEnum::Agent->value));
    }

    private function availableRoles(): array
    {
        return Auth::user()->role === UserRoleEnum::Admin
            ? UserRoleEnum::cases()
            : [UserRoleEnum::Agent];
    }

    private function availableAgencies()
    {
        $actor = Auth::user();

        return Agency::query()
            ->when($actor->role !== UserRoleEnum::Admin, fn ($query) => $query->whereKey($actor->agency_id))
            ->orderBy('name')
            ->get();
    }

    private function enforceRoleRules(array $data): array
    {
        $actor = Auth::user();

        if ($actor->role !== UserRoleEnum::Admin) {
            $data['role'] = UserRoleEnum::Agent->value;
            $data['agency_id'] = $actor->agency_id;
        }

        return $data;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'showForm',
            'first_name',
            'last_name',
            'email',
            'phone',
            'password',
            'password_confirmation',
            'agency_id',
        ]);

        $this->role = UserRoleEnum::Agent->value;
        $this->is_active = true;
        $this->resetValidation();
    }
}
