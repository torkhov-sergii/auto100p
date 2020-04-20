@extends('layout.default')

@section('content')

    @if ($post)

        <main class="single-article">
            <section class="text-page">
                <div class="container single-container">

                    {!! kama_breadcrumbs('') !!}

                    @if(!empty($fields['single_new_main_image']))
                    <div class="text-page__image corner-cut corner-cut_white">
                        <img src="{{ $fields['single_new_main_image']['url'] }}" alt="{{ $fields['single_new_main_image']['alt'] }}">
                    </div>
                    @endif
                    <time class="text-page__time">{{ date('d.m.Y', strtotime($post->post_date)) }}</time>
                    <h1 class="text-page__title">{!! $post->title() !!}</h1>
                    <div class="text-page__share">
                        <span>Поделиться</span>
                        {!! do_shortcode('[addtoany]') !!}
                    </div>
                    <div class="text-page__content">
                        {!! $post->content() !!}
                    </div>
                </div>
            </section>
            <section class="news-section"
                     style="background-image: url(/wp-content/themes/classy/dist/img/news_bg.jpg);">
                <div class="container">
                    <div class="sect-title">
                        <h2>Похожие новости</h2>
                    </div>
                    <div class="news-block d-flex js-news flex-wrap">
                        @foreach($news as $one_news)
                            <div class="col-lg-3 col-md-6 col-12">
                                <a href="{{ $one_news->permalink() }}">
                                    <article class="news-block__item article-card js-article">
                                        <div class="article-card__inner">
                                            <div class="article-card__image">
                                                <img src="{{ $one_news->thumbnail()->src(300,180) }}" alt="upg">
                                            </div>
                                            <div class="article-card__content">
                                                <time class="article-card__time">{{ date('d.m.Y', strtotime($one_news->post_date)) }}</time>

                                                <h3 class="article-card__title">
                                                    {{ $one_news->title() }}
                                                </h3>
                                                <div class="article-card__description">
                                                    {!! $one_news->excerpt !!}
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>
    @endif

@stop
