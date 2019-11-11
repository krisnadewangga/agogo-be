@extends('layouts.app1')

@section('content')
    @component('components.card', ['title' => 'Dashboard', 'breadcumbs' => array(
                                                                                  array('judul' => 'breadcumb1','link' => 'google.com'),
                                                                                  array('judul' => 'breadcumb2','link' => 'facebook.com')
                                                                                ) 
                                  ])
            
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            You are logged in!

            <a class="dropdown-item" href="{{ route('logout') }}"
               onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            
    @endcomponent
@endsection
