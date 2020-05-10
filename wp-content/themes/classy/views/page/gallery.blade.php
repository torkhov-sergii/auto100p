@extends('layout.default')

@section('content')
    <div class="gallery">
        <div class="container">
            <h1>
                {!! $page->title() !!}
            </h1>

            <div class="gallery__list js-magnific-popup-container">
                @foreach($page->getAcfByKey('images') as $image)
                    <a href="{{ \Helpers\Images::getFlyImage($image['ID'], [1500,1500], false) }}" class="gallery__item" style="background-image: url('{{ \Helpers\Images::getFlyImage($image['ID'], [300,300], false) }}')">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@stop
