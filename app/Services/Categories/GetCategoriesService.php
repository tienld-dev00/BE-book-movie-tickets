<?php

namespace App\Services\Categories;

use App\Interfaces\Categories\CategoriesRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class GetCategoriesService extends BaseService
{
    protected $CategoriesRepository;

    public function __construct(CategoriesRepositoryInterface $CategoriesRepository)
    {
        $this->CategoriesRepository = $CategoriesRepository;
    }

    public function handle()
    {
        try {
            $user = $this->CategoriesRepository->all();
            return $user;
        } catch (Exception $e) {
            Log::info($e);
        }
    }
}
