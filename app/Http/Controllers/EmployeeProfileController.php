<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('employee.profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('employee.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('employee.profile.show')
            ->with('success', 'Profilo aggiornato con successo.');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = Auth::user();
        
        if ($request->hasFile('photo')) {
            // Se esiste già una foto, elimina quella vecchia
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
            $user->save();
        }
        
        return redirect()->route('employee.profile.show')
            ->with('success', 'Foto profilo caricata con successo.');
    }
}