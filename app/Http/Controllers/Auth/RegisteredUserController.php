<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $barangays = Barangay::query()
            ->select('id', 'region', 'province', 'city', 'name')
            ->orderBy('region')
            ->orderBy('province')
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        $barangayOptions = $barangays->map(function ($barangay) {
            return [
                'id' => $barangay->id,
                'region' => $barangay->region ?: 'Unspecified Region',
                'province' => $barangay->province ?: 'No Province / NCR',
                'city' => $barangay->city,
                'name' => $barangay->name,
            ];
        })->values();

        return view('auth.register', compact('barangayOptions'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],

            'barangay_id' => [
                'required',
                'integer',
                'exists:barangays,id',
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $barangay = Barangay::findOrFail($validated['barangay_id']);

        // Keep the existing address field populated for compatibility.
        $address = $barangay->name . ', ' . $barangay->city;

        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->address = $address;
        $user->barangay_id = $barangay->id;
        $user->role = 'resident';
        $user->password = Hash::make($validated['password']);

        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}