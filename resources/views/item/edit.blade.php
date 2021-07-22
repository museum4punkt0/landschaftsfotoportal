@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

@include('includes.modal_alert')
@include('includes.item_edit')

@endsection
