@extends('layouts.error', ['code' => 404, 'title' => __('errors.404.title')])

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 7H13V13H11V7ZM11 15H13V17H11V15Z" fill="url(#gradient)"/>
        <defs>
            <linearGradient id="gradient" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
            </linearGradient>
        </defs>
    </svg>
@endsection

@section('message')
    <span>@lang('errors.404.message.p1')</span>
    <span>@lang('errors.404.message.p2')</span>
@endsection
