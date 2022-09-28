@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    @include('includes.alert_session_div')

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
                
                <div class="table-responsive">
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
                                    <a href="#" class="btn btn-outline-danger btn-sm"
                                        data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('value.destroy', $value) }}"
                                        data-message="@lang('values.confirm_delete', ['name' => $value->value])"
                                        data-title="@lang('values.delete')"
                                        title="@lang('common.delete')"
                                    >
                                        @lang('common.delete')
                                    </a>
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
                                <span class="d-md-table-cell fa-btn">
                                    <span class="fa-stack fa-2x">
                                        <a href="{{ route('element.edit', $element) }}" title="@lang('common.edit')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                        </a>
                                    </span>
                                    @if($element->parent_fk)
                                        <a href="{{ route('element.show', $element->parent_fk) }}">
                                            {{$element->parent_fk}}
                                        </a>
                                    @else
                                        @lang('common.root')
                                    @endif
                                </span>
                            </td>
                        @endif
                        <td>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('element.destroy', $element) }}"
                                        data-message="@lang('elements.confirm_delete', ['name' => $element->element_id])"
                                        data-title="@lang('elements.delete')"
                                        title="@lang('common.delete')"
                                    >
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_delete') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                        </td>
                    </tr>
                </tbody>
                </table>
                </div>
            </div>
        @else
            <div class="card-body">
                <h3>You need to log in. <a href="{{url()->current()}}/login">Click here to login</a></h3>
            </div>
        @endif
        <div class="card-footer">
        </div>
    </div>
</div>

@endsection
