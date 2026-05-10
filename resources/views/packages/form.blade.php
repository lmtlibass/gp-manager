@extends('layouts.app')

@section('content')
    <livewire:packages.form :package="$package ?? null" />
@endsection
