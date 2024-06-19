<?php

namespace App\Repositories\Categories;

use App\Interfaces\Categories\CategoriesRepositoryInterface;
use App\Models\Category;
use App\Repositories\BaseRepository;

class CategoriesRepository extends BaseRepository implements CategoriesRepositoryInterface
{
    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function search($valueSearch)
    {
        return $this->model->where('name', 'like', $valueSearch)->get();
    }
}
