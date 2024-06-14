<?php

namespace App\Services\User;

use App\Interfaces\User\UserRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class UpdateUserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle()
    {
        try {
            dump('Update UserService');

            // $this->userRepository->update($this->data, $this->data->id);
            return true;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
