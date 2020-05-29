@extends('layout.default')

@section('content')

    <div class="services main">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="services__list">
                        @foreach($services as $service)
                            <div class="services__item item-services {{ ($service->ID == $post->ID) ? 'active': '' }}">
                                <a href="{{ $service->get_permalink() }}" class="">{{ $service->title() }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-9">
                    @if ($post)
                        <div class="services__main">
                            @if(!empty($fields['single_new_main_image']))
                                <div class="text-page__image corner-cut corner-cut_white">
                                    <img src="{{ $fields['single_new_main_image']['url'] }}" alt="{{ $fields['single_new_main_image']['alt'] }}">
                                </div>
                            @endif

                            <h1 class="services__title">{!! $post->title() !!}</h1>

                            <div class="services__content">
                                {!! $post->content() !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop
