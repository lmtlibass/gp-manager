<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.login', ['mode' => 'register']);
    }

    public function register(RegisterRequest $request)
    {
        // Generate a unique agency code from the name
        $baseCode = strtoupper(Str::slug($request->agency_name, ''));
        $baseCode = Str::limit($baseCode, 6, '');
        $code = $baseCode . '-' . strtoupper(Str::random(4));

        while (Agency::where('code', $code)->exists()) {
            $code = $baseCode . '-' . strtoupper(Str::random(4));
        }

        $agency = Agency::create([
            'name'             => $request->agency_name,
            'code'             => $code,
            'phone'            => $request->agency_phone,
            'whatsapp_number'  => $request->agency_phone,
            'city'             => $request->agency_city,
            'country'          => $request->agency_country,
            'is_active'        => true,
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => $request->password,
            'phone'      => $request->phone,
            'agency_id'  => $agency->id,
            'role'       => UserRoleEnum::GpManager->value,
            'is_active'  => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', "Bienvenue {$user->first_name} ! Votre compte GP Manager a été créé.");
    }
}
