<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Categories\CreateRequest;
use App\Http\Requests\Api\Categories\UpdateRequest;
use App\Services\Categories\CreateCategoriesService;
use App\Services\Categories\GetCategoriesService;
use App\Services\Categories\SearchCategoriesService;
use App\Services\Categories\ShowIdCategoriesService;
use App\Services\Categories\UpdateCategoriesService;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return HttpResponse
     */
    public function store(CreateRequest $request)
    {
        $data = resolve(CreateCategoriesService::class)->setParams($request->validated())->handle();
        if (!$data) {
            return $this->responseErrors(__('Categories.create_fail'));
        }

        return $this->responseSuccess([
            'message' => __('Categories.create_success'),
            'data' => $data,
        ]);
    }

    /**
     * Get a listing of the resource or search Categories.
     *
     * @return HttpResponse
     */
    public function index(Request $request)
    {
        $valueSearch = $request->query('valueSearch');

        if ($valueSearch) {
            $valueSearch = '%' . $valueSearch . '%';
            $data = resolve(SearchCategoriesService::class)->setParams($valueSearch)->handle();

            if (!$data) {
                return $this->responseErrors(__('Categories.search_fail'));
            }

            return $this->responseSuccess([
                'message' => __('Categories.search_success'),
                'data' => $data,
            ]);
        } else {
            $data = resolve(GetCategoriesService::class)->handle();

            if (!$data) {
                return $this->responseErrors(__('Categories.get_fail'));
            }

            return $this->responseSuccess([
                'message' => __('Categories.get_success'),
                'data' => $data,
            ]);
        }
    }

    public function showCategories($id)
    {
        $data = resolve(ShowIdCategoriesService::class)->setParams($id)->handle();
        if (!$data) {
            return $this->responseErrors(__('Categories.showCategories_fail'));
        }

        return $this->responseSuccess([
            'message' => __('Categories.showUser_success'),
            'data' => $data,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $update = $request->validated();
        $update['id'] = $id;

        $data = resolve(UpdateCategoriesService::class)->setParams($update)->handle();

        if (!$data) {
            return $this->responseErrors(__('Categories.update_fail'));
        }

        return response()->json([
            'message' => __('Categories.update_success'),
            'data' => $data,
        ]);
    }
}
