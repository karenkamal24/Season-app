<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use App\Http\Resources\Auth\ProfileResource;
use App\Utils\ApiResponse;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show()
    {
        $user = $this->profileService->getProfile();

        return ApiResponse::send(
            Response::HTTP_OK,
            'Profile data retrieved successfully.',
            new ProfileResource($user)
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->profileService->updateProfile($request);

        return ApiResponse::send(
            Response::HTTP_OK,
            'Profile updated successfully.',
            new ProfileResource($user)
        );
    }
}
