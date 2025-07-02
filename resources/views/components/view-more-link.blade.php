@props([
    'link' => '#',
    'btn' => 'news'
])
<div class="col-md-12 text-end m-2">
<a href="{{ $link }}" style="border-bottom:1px solid black"><strong>View more {{$btn}}</strong></a>
</div>
