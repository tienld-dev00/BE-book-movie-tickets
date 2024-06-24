<?php

namespace App\Interfaces\Movie;

use App\Interfaces\CrudRepositoryInterface;

interface MovieRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * show showtime by slug (for admin) 
     *
     * @param  int $slug
     * @return Resource
     */
    public function getMovie($slug);

    /**
     * show showtime by slug (for client) 
     *
     * @param  int $slug
     * @return Resource
     */
    public function getMovieClient($slug);

    /**
     * get list movies (for admin) 
     *
     * @param  array $data
     * @return ResourceCollection
     */
    public function getListMovies($data);

    /**
     * get list movies (for client)
     *
     * @param  array $data
     * @return ResourceCollection
     */
    public function getListMoviesClient($data);
}
