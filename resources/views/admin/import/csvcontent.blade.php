@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">@lang('import.header')</div>
        <div class="card-body">

            <form action="{{ route('import.csv.process') }}" method="POST" class="form-horizontal">
                {{ csrf_field() }}
                <input type="hidden" name="list" value="{{ $list->list_id }}" />

                <table class="table mt-4">
                    
                    @foreach ($csv_data as $row)
                        <tr>
                        @foreach ($row as $key => $value)
                            <td>{{ $value }}</td>
                        @endforeach
                        </tr>
                    @endforeach
                    <tr>
                    @foreach ($csv_data[0] as $key => $value)
                        <td>
                            <select name="fields[{{ $key }}]">
                                <option value="0">@lang('common.ignore')</option>
                                @if($list->hierarchical)
                                    <option value="-1">@lang('import.element_id')</option>
                                    <option value="-2">@lang('import.parent_id')</option>
                                @endif
                                @foreach ($attributes as $attr)
                                    <option value="{{ $attr->attribute_id }}">{{ $attr->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    @endforeach
                    </tr>
                    <tr><td colspan={{ sizeof($csv_data[0]) }}>
                        <span class="text-danger">{{ $errors->first('fields.*') }}</span>
                        
                    </td></tr>
                </table>

                <div class="form-group">
                <span>
                    @lang('import.attribute_hint')<br/>
                    @lang('import.into_this_list', ['name'=>$list->name, 'description'=>$list->description])
                </span>
                </div>

                <div class="form-group">
                    <input type="checkbox" name="header" class="checkbox" value=1 @if(old('header')) checked @endif />
                    <span>@lang('import.contains_header')</span>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('import.import')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
