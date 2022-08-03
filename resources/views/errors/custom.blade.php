@extends('layouts.minimal')

@section('title', __('Server Error'))
@section('code', $code)
@section('message', __('Server Error') . ": " . $message)
