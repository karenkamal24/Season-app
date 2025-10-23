<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use App\Http\Resources\Auth\ProfileResource;
use App\Utils\ApiResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LangHelper;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show()
    {
        $user = Auth::user();

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('profile_retrieved'),
            new ProfileResource($user)
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->profileService->updateProfile($request);

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('profile_updated'),
            new ProfileResource($user)
        );
    }
}
