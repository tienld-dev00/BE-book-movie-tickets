<?php

namespace App\Services\Categories;

use App\Interfaces\Categories\CategoriesRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class DeleteCategoriesService extends BaseService
{
    protected $CategoriesRepository;

    public function __construct(CategoriesRepositoryInterface $CategoriesRepository)
    {
        $this->CategoriesRepository = $CategoriesRepository;
    }

    public function handle()
    {
        try {
            $this->CategoriesRepository->delete($this->data->id);
            return true;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
