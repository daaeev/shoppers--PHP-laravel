<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GetAllForeignInterface;
use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;

interface SizeRepositoryInterface extends
    GridInterface,
    GetAllForeignInterface,
    GetFirstInterface
{

}
