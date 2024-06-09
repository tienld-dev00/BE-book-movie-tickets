<?php

namespace App\Interfaces\Order;

use App\Interfaces\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

interface OrderRepositoryInterface extends CrudRepositoryInterface
{
    // public function applyFilter(Builder $builder, string $column, string $value, string $operator = '=');

    public function applySearch(Builder $builder, string $value);

    // public function applySort(Builder $builder, string $column, string $direction = 'asc');

    public function updateOrCreate(array $checkData, array $data);
}
