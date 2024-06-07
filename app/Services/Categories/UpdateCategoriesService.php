<?php

namespace App\Services\Categories;

use App\Interfaces\Categories\CategoriesRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class UpdateCategoriesService extends BaseService
{
    protected $CategoriesRepository;

    public function __construct(CategoriesRepositoryInterface $CategoriesRepository)
    {
        $this->CategoriesRepository = $CategoriesRepository;
    }

    public function handle()
    {
        try {
            return $this->CategoriesRepository->update($this->data, $this->data['id']);

        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
