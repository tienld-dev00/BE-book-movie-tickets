<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\CreateUserService;
use App\Services\User\DeleteUserService;
use App\Services\User\GetUserService;
use App\Services\User\UpdateUserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $user = resolve(CreateUserService::class)->setParams($request)->handle();
        return redirect()->route('users.index');
    }

    public function delete(int $id, Request $request)
    {
        $user = resolve(DeleteUserService::class)->handle();
        return redirect()->route('users.index');
    }

    public function index(Request $request)
    {
        $user = resolve(GetUserService::class)->handle();
        return redirect()->route('users.index');
    }

    public function update(Request $request)
    {
        $user = resolve(UpdateUserService::class)->setParams($request)->handle();
        return redirect()->route('users.index');
    }
}
