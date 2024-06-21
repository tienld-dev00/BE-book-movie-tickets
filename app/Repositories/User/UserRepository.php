<?php

namespace App\Repositories\User;

use App\Interfaces\User\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function searchByRole($role, $valueSearch = null)
    {
        $query = $this->model->where('role', $role);

        if ($valueSearch) {
            $query->where(function ($q) use ($valueSearch) {
                $q->where('name', 'like', $valueSearch)
                  ->orWhere('email', 'like', $valueSearch);
            });
        }

        return $query->get();
    }
}
