<?php
    $pageNum = 1;

    $curPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $nextPageUrl = $paginator->nextPageUrl();
    $prevPageUrl = $paginator->previousPageUrl();
?>

@if ($lastPage > 1)
    <div class="row" data-aos="fade-up">
        <div class="col-md-12 text-center">
            <div class="site-block-27">
                <ul>
                    <li><a href="{{$prevPageUrl}}" @if($curPage == 1) style="pointer-events: none;" @endif><</a></li>

                    @while($pageNum <= $lastPage)
                        <li @if($curPage == $pageNum) class="active" @endif><a href="{{$paginator->url($pageNum)}}" @if($curPage == $pageNum) style="pointer-events: none;" @endif>{{$pageNum}}</a></li>

                        <?php $pageNum++ ?>
                    @endwhile

                    <li><a href="{{$nextPageUrl}}" @if($curPage == $lastPage) style="pointer-events: none;" @endif>></a></li>
                </ul>
            </div>
        </div>
    </div>
@endif
