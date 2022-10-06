@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('revisions.moderated')</div>
            <div class="card-body">
                
                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1"></th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="1">@lang('items.item_type')</th>
                        <th colspan="1">@lang('common.updated')</th>
                        <th colspan="1">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($items->sortByDesc('updated_at') as $item)
                    <tr>
                        <td>
                            {{$item->item_id}}
                        </td>
                        <td>
                            <div class="container">
                            <a href="{{route('item.show.public', $item->item_fk)}}#details">
                            @if($item->details->firstWhere('column_fk', 13))
                                <img class="img-fluid thumbnail-table"
                                    src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $item->details->firstWhere('column_fk', 13)->value_string) }}"
                                    alt=""
                                    title="{{ $item->details->firstWhere('column_fk', 23)->value_string }}"/>
                            @endif
                            </a>
                            </div>
                        </td>
                        <td>
                            <a href="{{route('item.show.public', $item->item_fk)}}"
                                title="@lang('items.show_frontend')">
                                {{$item->title}}
                            </a>
                        </td>
                        <td>
                            @foreach($item->item_type->values as $v)
                                {{$v->value}}<br/>
                            @endforeach
                            Typ-ID {{$item->item_type_fk}}
                        </td>
                        <td>
                            {{$item->editor->name}}, @lang('revisions.revision'):
                            @if($item->revision < 0)@lang('revisions.draft')@endif{{$item->revision}},
                            {{$item->updated_at}}
                        </td>
                        <td>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('revision.show', $item) }}" title="@lang('common.show')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_show') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('revision.edit', $item) }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('revision.destroy.draft', $item) }}"
                                        data-message="@lang('revisions.confirm_delete', ['name' => $item->title])"
                                        data-title="@lang('revisions.delete')"
                                        title="@lang('common.delete')"
                                    >
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_delete') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                        </td>
                    </tr>
                @endforeach
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
            {{ $items->links() }}
        </div>
    </div>
</div>

@endsection
