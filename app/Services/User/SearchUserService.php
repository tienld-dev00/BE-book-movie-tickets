<?php

namespace App\Services\User;

use App\Interfaces\User\UserRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class SearchUserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle()
    {
        try
        {
            return $this->userRepository->search($this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
