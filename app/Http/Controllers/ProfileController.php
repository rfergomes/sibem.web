<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $tokens = \App\Models\TokenV2::with('local')
            ->where('user_id', $user->id)
            ->where('ativo', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('perfil.edit', compact('user', 'tokens'));
    }

    /**
     * Update the user profile in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:100|unique:mysql_sys.users_v2,email,' . $user->id,
            'telefone' => 'nullable|string|max:30',
            'igreja' => 'nullable|string|max:200',
            'cidade' => 'nullable|string|max:200',
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Por favor, informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário.',
            'password.min' => 'A nova senha deve conter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
            'foto.image' => 'O arquivo enviado deve ser uma imagem.',
            'foto.mimes' => 'A imagem do perfil deve estar em um dos formatos: jpeg, png, jpg ou gif.',
            'foto.max' => 'A imagem de perfil não pode ser maior que 2MB.',
        ]);

        // Update fields
        $user->name = $request->name;
        $user->email = $request->email;
        $user->telefone = $request->telefone;
        $user->igreja = $request->igreja;
        $user->cidade = $request->cidade;

        // Update password if filled
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Process profile photo if uploaded
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Save new photo
            $path = $request->file('foto')->store('profiles', 'public');
            $user->foto = $path;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Perfil atualizado com sucesso.');
    }
}
