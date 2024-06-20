<?php

namespace App\Services\User;

use App\Enums\UserRole;
use App\Interfaces\User\UserRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class GetUserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle()
    {
        try {
            $user = $this->userRepository->searchByRole(UserRole::USER);
            return $user;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
