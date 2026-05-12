<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class PackagePageController extends Controller
{
    public function show(Package $package): View
    {
        Gate::authorize('view', $package);

        return view('packages.show', ['package' => $package]);
    }

    public function edit(Package $package): View
    {
        Gate::authorize('update', $package);

        return view('packages.form', ['package' => $package]);
    }
}
