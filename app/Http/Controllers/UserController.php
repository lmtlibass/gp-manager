<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function showProfile()
    {
        return view('user.profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated = $request->validated();

        $user->update(array_merge($validated, [
            'name' => $this->fullName($validated['first_name'], $validated['last_name']),
        ]));

        return redirect()->route('user.profile')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    /**
     * Afficher la page d'index des utilisateurs (admin/gp_manager)
     */
    public function index()
    {
        $users = User::paginate(15);

        return view('user.index', [
            'users' => $users,
        ]);
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     */
    public function create()
    {
        return view('user.create', [
            'agencies' => Agency::all(),
        ]);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(CreateUserRequest $request)
    {
        $validated = $request->validated();

        User::create(array_merge($validated, [
            'name' => $this->fullName($validated['first_name'], $validated['last_name']),
        ]));

        return redirect()->route('users.index')
            ->with('success', 'L\'utilisateur a été créé avec succès.');
    }

    /**
     * Afficher un utilisateur spécifique
     */
    public function show(User $user)
    {
        return view('user.show', [
            'user' => $user,
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur
     */
    public function edit(User $user)
    {
        return view('user.edit', [
            'user' => $user,
            'agencies' => Agency::all(),
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required'],
            'agency_id' => ['required', 'exists:agencies,id'],
            'is_active' => ['boolean'],
        ]);

        $user->update(array_merge($validated, [
            'name' => $this->fullName($validated['first_name'], $validated['last_name']),
        ]));

        return redirect()->route('users.show', $user)
            ->with('success', 'L\'utilisateur a été mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'L\'utilisateur a été supprimé avec succès.');
    }

    private function fullName(string $firstName, string $lastName): string
    {
        return trim($firstName.' '.$lastName);
    }
}
