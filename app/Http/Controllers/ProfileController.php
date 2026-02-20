<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ]);

        $user->nome = $validated['nome'];
        $user->email = $validated['email'];

        if ($request->filled('notification_settings')) {
            $user->notification_settings = $request->input('notification_settings');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle Remove Avatar
        if ($request->has('remove_avatar') && $request->remove_avatar) {
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
                $user->avatar = null;
            }
        }

        $user->save();

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
