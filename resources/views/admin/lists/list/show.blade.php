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
            <div class="card-header">@lang('lists.edit'): {{$list->name}} ({{$list->description}})</div>
            <div class="card-body">
                @if($list->internal)
                    <div class="alert alert-warning">@lang('lists.internal_warning')</div>
                @endif

                <div class="row">
                    <div class="col align-self-start">
                        <a href="{{route('list.element.create', $list->list_id)}}" class="btn btn-primary">@lang('elements.new')</a>
                        <a href="{{route('list.export', $list->list_id)}}" class="btn btn-primary">@lang('common.export')</a>
                    </div>
                    
                    @include('includes.form_autocomplete_search', [
                        'search_url' => route('element.autocomplete', $list->list_id),
                        'div_class' => 'col align-self-end',
                        'input_placeholder' => __('search.search'),
                    ])
                </div>
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('lists.rank')</th>
                        <th colspan="1">@lang('lists.attribute'): @lang('values.value')</th>
                    @if($list->hierarchical)
                        <th colspan="1">@lang('lists.parent') @lang('common.id')</th>
                    @endif
                        <th colspan="2">@lang('elements.element')</th>
                    </tr>
                </thead>
                <tbody>
                
                @foreach($elements as $element)
                    <tr>
                        <td>
                            {{$element->element_id}}
                        </td>
                        <td>
                            {{$element->depth + 1}}
                        </td>
                        <td>
                            <table style="margin-left:{{ $element->depth*50 }}px;">
                            @foreach($element->values as $value)
                                <tr>
                                <td>
                                    <a href="{{route('value.edit', $value->value_id)}}" class="btn btn-outline-primary btn-sm">@lang('common.edit')</a>
                                </td>
                                <td>
                                    <form action="{{route('value.destroy', $value->value_id)}}" method="POST">
                                        {{ csrf_field() }}
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">@lang('common.delete')</button>
                                    </form>
                                </td>
                                <td>{{$value->attribute['name']}}: <strong>{{$value->value}}</strong></td>
                                </tr>
                            @endforeach
                                <tr><td colspan=3>
                                    <a href="{{route('element.value.create', $element->element_id)}}" class="btn btn-outline-primary btn-sm">@lang('values.new')</a>
                                </td></tr>
                            </table>
                        </td>
                        @if($list->hierarchical)
                            <td>
                                <form action="{{route('element.edit', $element->element_id)}}" method="GET">
                                    {{ csrf_field() }}
                                    <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                                    {{$element->parent_fk}}
                                </form>
                            </td>
                        @endif
                        <td>
                            <form action="{{route('element.destroy', $element->element_id)}}" method="POST">
                                {{ csrf_field() }}
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">@lang('common.delete')</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                </table>
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
