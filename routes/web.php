<?php

use Illuminate\Support\Facades\Route;
use App\Services\FirebaseService;
use App\Http\Controllers\WebPageController;

Route::get('/', function () {
    return view('welcome');
});

// Legal Pages
Route::get('/terms', [WebPageController::class, 'terms'])->name('terms');
Route::get('/privacy', [WebPageController::class, 'privacy'])->name('privacy');

Route::get('/test-fcm', function (FirebaseService $firebase) {
    $fcmToken = 'eSmeYT1US2Gz6t2QiTlW1W:APA91bERIOgIkyQf1OQsN5qcuhDALyhYl7-uQSgDVWOI-0-0NlSABf4kYBhbicciqkoQlqev94bU6YuoK-VyyvJaHp8FqqWJ0khgVLLHeZet4Q0oLQq9RCs';
    return $firebase->sendToDevice(
        $fcmToken,
        '🔥 Laravel Test',
        'Notification from Laravel Backend',
        ['type' => 'test']
    );
});
