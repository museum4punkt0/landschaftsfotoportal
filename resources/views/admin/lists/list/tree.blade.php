@extends('layouts.app')

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container">
    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">
                @lang('lists.edit'): {{$list->name}} ({{$list->description}})
            </div>
            <div class="card-body">
                <a href="{{route('list.element.create', $list->list_id)}}">@lang('elements.new')</a>
                <ul class="list-group">
                    @foreach ($elements as $element)
                    <li class="list-group-item">
                        @for ($i = 0; $i < $element->depth; $i++)
                            |---
                        @endfor
                        @foreach($element->values as $value)
                            {{$value->attribute['name']}}: 
                            <a href="{{route('value.edit', $value->value_id)}}" title="@lang('common.edit')">{{$value->value}}</a>
                            <br/>
                        @endforeach
                        <a href="{{route('element.value.create', $element->element_id)}}">@lang('common.new')</a>
                        
                        @if($list->hierarchical)
                            <a href="{{route('element.edit', $element->element_id)}}">@lang('common.edit')</a>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="card-body">
                <h3>You need to log in. <a href="{{url()->current()}}/login">Click here to login</a></h3>
            </div>
        @endif
        <div class="card-footer">
            {{ $elements->links() }}
        </div>
    </div>
</div>

@endsection
