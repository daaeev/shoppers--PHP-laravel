<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;

interface NewsRepositoryInterface extends
    GridInterface,
    GetFirstInterface
{

}
