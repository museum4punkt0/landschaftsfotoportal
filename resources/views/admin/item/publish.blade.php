@extends('layouts.app')

@section('content')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('items.unpublished')</div>
            <div class="card-body">
                <a href="{{route('item.new')}}" class="btn btn-primary">@lang('items.new')</a>
                @unless(config('ui.revisions'))
                    <a href="{{route('item.publish')}}" class="btn btn-primary">@lang('common.publish_all')</a>
                @endunless
                
                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1"></th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="1">@lang('items.item_type')</th>
                        <th colspan="1">@lang('common.updated')</th>
                        <th colspan="4">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            {{$item->item_id}}
                        </td>
                        <td>
                            <div class="container">
                            <a href="{{route('item.show.public', $item->item_id)}}#details">
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
                            <a href="{{route('item.show.public', $item->item_id)}}"
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
                            {{$item->editor->name}}, {{$item->updated_at}}
                        </td>
                        <td>
                        @unless(config('ui.revisions'))
                            <a href="{{route('item.publish', $item->item_id)}}" class="btn btn-primary">
                            @lang('common.publish')
                            </a>
                        @endunless
                        </td>
                        <td>
                            <form action="{{route('item.show', $item->item_id)}}" method="GET">
                                <button class="btn btn-primary" type="submit">@lang('common.show')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('item.edit', $item)}}" method="GET">
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('item.destroy', $item)}}" method="POST">
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
