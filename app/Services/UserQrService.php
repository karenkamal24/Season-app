<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class UserQrService
{
    public function generate(User $user): string
    {

        if ($user->qr_code_path && Storage::disk('public')->exists($user->qr_code_path)) {
            return asset('storage/' . $user->qr_code_path);
        }


        $qrData = json_encode([
            'user_id'  => $user->id,
            'name'     => $user->name,
            'nickname' => $user->nickname,
            'email'    => $user->email,
            'phone'    => $user->phone,

        ]);


        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrData);


        $path = 'qrcodes/user_' . $user->id . '.svg';


        Storage::disk('public')->put($path, $svg);


        $user->update(['qr_code_path' => $path]);

      
        return asset('storage/' . $path);
    }
}
