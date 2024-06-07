<?php

namespace App\Interfaces\Categories;
use App\Interfaces\CrudRepositoryInterface;

interface CategoriesRepositoryInterface extends CrudRepositoryInterface
{
    public function search($valueSearch);
}
