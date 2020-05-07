@extends('layout.default')

@section('content')
    <div class="gallery">
        <div class="container">
            <h1>
                {!! $page->title() !!}
            </h1>

            @foreach($page->getAcfByKey('images') as $image)
                <img src="{{ $page->getFlyImage($image->ID, [50,50], false) }}">
            @endforeach
        </div>
    </div>
@stop
