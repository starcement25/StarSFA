@extends('layouts.default')

@section('content')

<h1>Dashboard</h1><a href="{{ url('/logout') }}">Logout</a>
@if(Session::has('USERNAME'))
    Hello {{ Session::get('USERNAME')}}
@endif




@endsection
