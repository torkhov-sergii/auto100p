@extends('layout.default')

@section('content')

   <div class="p-home">
      <div class="container">
         <h1>HOME</h1>

         <div class="p-home__content">
            <b>title:</b>
            {!! $page->title() !!}
         </div>

         <div class="p-home__content">
            <b>content preview:</b>
            {{ $page->get_preview(50, false, '...', true) }}
         </div>

         <div class="p-home__content">
            <b>content:</b>
            {!! $page->content() !!}
         </div>

         <div class="p-home__content">
            <b>permalink:</b>
            {!! $page->permalink() !!}
         </div>

         <div class="p-home__content">
            <b>getAcfByKey:</b>
            {!! $page->getAcfByKey('test') !!}
         </div>

         <div class="p-home__content">
            <b>ACF image:</b>
            <img src="{{ $page->getAcfImage('test_image')->src('large') }}">
         </div>

         <div class="p-home__content">
            <b>POST thumbnail:</b>
            <img src="{{ $page->thumbnail()->src('medium') }}">
         </div>

         <div class="p-home__content">
            <b>getFlyImage:</b>
            <img src="{{ $page->getFlyImage($page->thumbnail()->ID, [50,50], false) }}">
            <img src="{{ $page->getFlyImage($page->getAcfImage('test_image')->ID, [50,50], false) }}">
         </div>

         <div class="p-home__content">
            <b>date:</b>
            {!! $page->getDate() !!}
         </div>
      </div>
   </div>

@stop

