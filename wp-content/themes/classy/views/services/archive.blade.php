@extends('layout.default')

@section('content')

    <main class="blog-page">
        <section class="blog-heading">
            <div class="container">
                <div class="d-flex justify-content-between">
                    <h1 class="blog-page__title">Новости</h1>
                    @include('partials.b-search')
                </div>
            </div>
        </section>
        <section class="blog-tags">
            <div class="container">
                <div class="tags d-flex flex-wrap">
                    <div class="js-tag tags__item js-all-tags selected">Все</div>
                    @foreach($tags as $tag )
                        <div class="js-tag tags__item" data-tag-id="{{ $tag->term_id }}">
                            {{ $tag->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="blog-articles">
            <div class="ajax-loader">
                <img src="/wp-content/themes/classy/dist/img/loader.gif" alt="upg">
            </div>
            <div class="container">
                <div class="d-flex flex-wrap row js-ajax-news">
                    @foreach($news as $one_news)
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12 article-card__outer js-ajax-article">
                            <a class="js-ajax-link" href="{{ $one_news->permalink() }}">
                                <article class="article-card">
                                    <div class="article-card__inner">
                                        <div class="article-card__image">
                                            <img class="js-ajax-image"
                                                 src="{{ $one_news->thumbnail()->src(310,180) }}" alt="upg">
                                        </div>
                                        <div class="article-card__content">
                                            <time class="article-card__time js-ajax-time">{{ date('d.m.Y', strtotime($one_news->post_date)) }}</time>

                                            <h3 class="article-card__title js-ajax-title">
                                                {{ $one_news->title() }}
                                            </h3>

                                        </div>
                                    </div>
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="js-pagination">
                    {!! $pagination !!}
                </div>

                {{ the_posts_pagination() }}
            </div>
        </section>
    </main>

@stop
