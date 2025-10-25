<?php

namespace App\Http\Controllers\Api\QR;


use App\Http\Controllers\Controller;
use App\Http\Requests\UserQrRequest\UserQrRequest;
use App\Http\Resources\UserQrResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\UserQrService;
use App\Helpers\LangHelper;
use App\Utils\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class UserQrController extends Controller
{
    protected UserQrService $qrService;

    public function __construct(UserQrService $qrService)
    {
        $this->qrService = $qrService;
    }

   public function generate()
    {
        try {

            $user = Auth::user();

            if (!$user) {
                return ApiResponse::badRequest(LangHelper::msg('invalid_credentials'));
            }


            $qrUrl = $this->qrService->generate($user);
            $user->qr_code_url = $qrUrl;

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('qr_generated_successfully'),
                new UserQrResource($user)
            );
        } catch (\Exception $e) {
            return ApiResponse::badRequest(LangHelper::msg('qr_generate_failed'), [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
