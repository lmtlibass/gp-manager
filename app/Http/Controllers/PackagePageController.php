<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PackagePageController extends Controller
{
    public function show(Package $package): View
    {
        $this->authorizePackage($package);

        return view('packages.show', ['package' => $package]);
    }

    public function edit(Package $package): View
    {
        $this->authorizePackage($package);

        return view('packages.form', ['package' => $package]);
    }

    private function authorizePackage(Package $package): void
    {
        if (Auth::user()->role->value !== 'admin' && $package->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }
    }
}
