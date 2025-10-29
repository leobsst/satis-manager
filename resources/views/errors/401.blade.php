@extends('layouts.error', ['code' => 401, 'title' => __('errors.401.title')])

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
        <path d="M12 1C8.676 1 6 3.676 6 7V8C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8V7C18 3.676 15.324 1 12 1ZM12 3C14.206 3 16 4.794 16 7V8H8V7C8 4.794 9.794 3 12 3ZM12 13C13.1 13 14 13.9 14 15C14 16.1 13.1 17 12 17C10.9 17 10 16.1 10 15C10 13.9 10.9 13 12 13Z" fill="url(#gradient)"/>
        <defs>
            <linearGradient id="gradient" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
            </linearGradient>
        </defs>
    </svg>
@endsection

@section('message')
    <span>@lang('errors.401.message.p1')</span>
    <span>@lang('errors.401.message.p2')</span>
@endsection
