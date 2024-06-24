<?php

namespace App\Interfaces\Movie;

use App\Interfaces\CrudRepositoryInterface;

interface MovieRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * show showtime by slug
     *
     * @param  int $slug
     * @return Resource
     */
    public function getMovie($slug);

    /**
     * get list movies
     *
     * @param  array $data
     * @return ResourceCollection
     */
    public function getListMovies($data);

    /**
     * Get the list of currently showing movies
     *
     * @param  array $data
     * @return ResourceCollection
     */
    public function listShowingMovies();

    /**
     * Get the list of upcoming movies
     *
     * @param  array $data
     * @return ResourceCollection
     */
    public function listUpcomingMovies();
}
