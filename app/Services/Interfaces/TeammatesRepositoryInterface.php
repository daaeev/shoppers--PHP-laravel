<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GetAllInterface;
use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;

interface TeammatesRepositoryInterface extends
    GetAllInterface,
    GetFirstInterface,
    GridInterface
{

}
