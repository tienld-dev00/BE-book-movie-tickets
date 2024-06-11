<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\Api\User\CreateRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Services\User\CreateUserService;
use App\Services\User\GetUserService;
use App\Services\User\SearchUserService;
use App\Services\User\ShowIdUserService;
use App\Services\User\UpdateUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return HttpResponse
     */
    public function store(CreateRequest $request)
    {
        $data = resolve(CreateUserService::class)->setParams($request->validated())->handle();
        if (!$data) {
            return $this->responseErrors(__('users.create_fail'));
        }

        return $this->responseSuccess([
            'message' => __('users.create_success'),
            'data' => $data,
        ]);
    }

    /**
     * Get a listing of the resource or search users.
     *
     * @return HttpResponse
     */
    public function index(Request $request)
    {
        $valueSearch = $request->query('valueSearch');

        if ($valueSearch) {
            $valueSearch = '%' . $valueSearch . '%';
            $data = resolve(SearchUserService::class)->setParams($valueSearch)->handle();

            if (!$data) {
                return $this->responseErrors(__('users.search_fail'));
            }

            return $this->responseSuccess([
                'message' => __('users.search_success'),
                'data' => $data,
            ]);
        } else {
            $data = resolve(GetUserService::class)->handle();

            if (!$data) {
                return $this->responseErrors(__('users.get_fail'));
            }

            return $this->responseSuccess([
                'message' => __('users.get_success'),
                'data' => $data,
            ]);
        }
    }

    public function showUser($id)
    {
        $data = resolve(ShowIdUserService::class)->setParams($id)->handle();
        if (!$data) {
            return $this->responseErrors(__('users.showUser_fail'));
        }

        return $this->responseSuccess([
            'message' => __('users.showUser_success'),
            'data' => $data,
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $update = $request->validated();
        $update['id'] = $id;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extention = $file->getClientOriginalExtension();
            $filename = time().'.'.$extention;
            $file->move('avatars', $filename);
            $update['avatar'] = $filename;
        }

        $data = resolve(UpdateUserService::class)->setParams($update)->handle();

        if (!$data) {
            return $this->responseErrors(__('users.update_fail'));
        }

        return response()->json([
            'message' => __('users.update_success'),
            'data' => $data,
        ]);
    }
}
