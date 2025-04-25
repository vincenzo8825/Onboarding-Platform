<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return view('employee.profile.show', [
            'user' => Auth::user()
        ]);
    }

    public function edit()
    {
        return view('employee.profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'bio' => 'nullable|string'
        ]);

        $user->update($validated);

        return redirect()->route('employee.profile.edit')
            ->with('success', 'Profilo aggiornato con successo!');
    }

    /**
     * Carica la foto profilo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        // Elimina la vecchia foto se esiste
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Salva la nuova foto
        $photoPath = $request->file('photo')->store('profile-photos/' . $user->id, 'public');

        $user->profile_photo = $photoPath;
        $user->save();

        return redirect()->route('employee.profile.show')
            ->with('success', 'Foto profilo caricata con successo!');
    }
}
