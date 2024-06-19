<?php

namespace App\Repositories\Movie;

use App\Enums\MovieFilter;
use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Models\Movie;
use App\Repositories\BaseRepository;
use App\Enums\MovieStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MovieRepository extends BaseRepository implements MovieRepositoryInterface
{
    public function __construct(Movie $movie)
    {
        $this->model = $movie;
    }

    /**
     * show showtime by slug 
     *
     * @param  int $slug
     * @return Resource
     */
    public function getMovie($slug)
    {
        $showtime = $this->model
            ->where('slug', $slug)
            ->where('status', MovieStatus::SHOW)
            ->first();

        return $showtime;
    }

    /**
     * get list movies 
     *
     * @param  array $data
     * @return ResourceCollection
     */
    public function getListMovies($data)
    {
        $perPage = $data['per_page'];
        $keyWord = $data['key_word'] ?? null;
        $sortField = $data['sort_field'];
        $sortDirection = $data['sort_direction'];

        $query = $this->model
            ->leftJoin('categories', 'movies.category_id', '=', 'categories.id')
            ->leftJoin('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->leftJoin('orders', function ($join) {
                $join->on('showtimes.id', '=', 'orders.showtime_id')
                    ->where('orders.status', true);
            })
            ->select('movies.*', 'categories.name as category_name', DB::raw('COUNT(orders.id) as total_orders'))
            ->groupBy('movies.id', 'category_name');

        if ($keyWord) {
            $query->where(function ($query) use ($keyWord) {
                $query->where('movies.name', 'LIKE', '%' . $keyWord . '%')
                    ->orWhere('movies.duration', 'LIKE', '%' . $keyWord . '%')
                    ->orWhere('movies.release_date', 'LIKE', '%' . $keyWord . '%')
                    ->orWhere('movies.age_limit', 'LIKE', '%' . $keyWord . '%')
                    ->orWhere('categories.name', 'LIKE', '%' . $keyWord . '%');
            });
        }

        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }
}
