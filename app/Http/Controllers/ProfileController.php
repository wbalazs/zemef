<?php

namespace App\Http\Controllers;

use Gate;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\SignCodeRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('profile.edit');
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        if (Gate::denies('update', auth()->user())) {
            return back()->withErrors(['not_allow_password' => 'Nincs jogosultsága erre a műveletre.']);
        }

        $user = auth()->user();
        $dataToUpdate = [];

        if ($request->hasFile('photo')) {
            $dataToUpdate['picture'] = $request->photo->store('profile', 'public');
        }

        // Ellenőrzés, hogy a kérés tartalmazza-e a képadatokat az aláíráshoz.
        if ($request->input('signature')) {
            $signatureData = $request->input('signature');
            $signatureData = explode(',', $signatureData)[1];
            $signatureData = base64_decode($signatureData);
            $signatureFileName = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($signatureFileName, $signatureData);
            $dataToUpdate['signpicture'] = $signatureFileName;
        }

        $user->update($dataToUpdate);

        return back()->withStatus('A profil sikeresen módosítva.');
    }

    /**
     * Change the sign code for the user
     */
    public function sign_code(SignCodeRequest $request)
    {
        if (Gate::denies('update', auth()->user())) {
            return back()->withErrors(['not_allow_password' => 'Nincs jogosultsága erre a műveletre.']);
        }

        auth()->user()->update(['sign_code' => $request->get('sign_code')]);

        return back()->withStatus('Az aláírókód sikeresen megváltoztatva.');
    }



    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        if (Gate::denies('update', auth()->user())) {
            return back()->withErrors(['not_allow_password' => 'Nincs jogosultsága erre a műveletre.']);
        }
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withPasswordStatus('A jelszó sikeresen megváltoztatva.');
    }
}
