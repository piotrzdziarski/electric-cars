@extends('layouts.app')
@section('title') Electric car sales announcements @endsection

@section('specialFonts')
    <link href="https://fonts.googleapis.com/css?family=Overpass:400,600,700&amp;subset=latin-ext" rel="stylesheet">
@endsection

@section('content')
    <announcements searching_settings='{{json_encode(Config::get('constants.SEARCHING_SETTING'))}}'></announcements>
@endsection