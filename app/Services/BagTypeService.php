<?php

namespace App\Services;

use App\Models\BagType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\LangHelper;

class BagTypeService
{
    /**
     * Get all active bag types
     */
    public function getAllBagTypes()
    {
        return BagType::where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    /**
     * Get bag type by ID
     */
    public function getBagTypeById(int $id)
    {
        $bagType = BagType::where('is_active', true)
            ->find($id);

        if (!$bagType) {
            throw new NotFoundHttpException(LangHelper::msg('bag_type_not_found'));
        }

        return $bagType;
    }
}

