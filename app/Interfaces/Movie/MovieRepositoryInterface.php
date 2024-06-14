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
}
