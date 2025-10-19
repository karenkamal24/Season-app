<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
public function updateProfile($request)
{
    $user = Auth::user();
    $data = $request->validated();

    if ($request->hasFile('photo_url')) {
        if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
            Storage::disk('public')->delete($user->photo_url);
        }

        $path = $request->file('photo_url')->store('avatars', 'public');
        $data['photo_url'] = $path;
    }
    $user->update($data);

    return $user->refresh();
}

}
