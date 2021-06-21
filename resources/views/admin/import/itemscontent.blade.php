@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">@lang('import.header'): @lang('items.header') ({{ $file_name }})</div>
        <div class="card-body">

            <form action="{{ route('import.items.process') }}" method="POST" class="form-horizontal">
                {{ csrf_field() }}
                <input type="hidden" name="item_type" value="{{ $colmaps[0]->item_type_fk }}" />

                <table class="table mx-0">
                    <tr>
                        <th>@lang('import.firstrow')</th>
                        <th>@lang('import.nextrows')</th>
                        <th>@lang('columns.header')</th>
                    </tr>
                    
                    @foreach($csv_data[0] as $csv_header)
                        <tr>
                            <td>
                                <b>{{ $csv_header }}</b>
                            </td>
                            <td>
                                @foreach($csv_data as $row)
                                    @unless($loop->first)
                                        {{ substr($row[$loop->parent->index], 0, 75) }} [...]<br>
                                    @endunless
                                @endforeach
                            </td>
                            <td>
                                <select name="fields[{{ $loop->index }}]">
                                    <option value="0">@lang('common.ignore')</option>
                                    <option value="-1"
                                        @if(old('fields.'.$loop->index) == -1) selected @endif>
                                        @lang('import.parent_details')
                                    </option>
                                    <option value="-2"
                                        @if(old('fields.'.$loop->index) == -2) selected @endif>
                                        @lang('import.parent_taxon')
                                    </option>
                                    <option value="-3"
                                        @if(old('fields.'.$loop->index) == -3) selected @endif>
                                        @lang('import.taxon_name')
                                    </option>
                                    @foreach($colmaps->unique('column_fk') as $colmap)
                                        {{-- Exclude columns with data type 'taxon' --}}
                                        @unless($colmap->column->data_type->attributes
                                            ->firstWhere('name', 'code')->pivot->value == '_taxon_')
                                            <option value="{{ $colmap->column_fk }}"
                                                @if(old('fields.'.$loop->parent->index, $selected_attr[$loop->parent->index]) == $colmap->column_fk)
                                                    selected
                                                @endif
                                            >
                                                {{ $colmap->column->translation->attributes
                                                    ->firstWhere('name', 'name_'.app()->getLocale())
                                                    ->pivot->value }}
                                            </option>
                                        @endunless
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                    
                    <tr><td colspan=3>
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </td></tr>
                </table>

                <div class="form-group">
                <span>
                    @lang('import.attribute_hint')<br/>
                </span>
                </div>

                <div class="form-group">
                    <input type="checkbox" name="header" class="checkbox" value=1 @if(old('header')) checked @endif />
                    <span>@lang('import.contains_header')</span>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="unique_taxa" class="checkbox" value=1 @if(old('unique_taxa')) checked @endif />
                    <span>@lang('import.unique_taxa')</span>
                </div>

                <div class="form-group">
                    <span>@lang('lists.parent')</span>
                    <select name="parent" class="form-control" size=1 >
                        <option value="">@lang('common.none')</option>
                        @foreach($items as $item)
                            <option value="{{$item->item_id}}"
                                @if(old('parent') == $item->item_id) selected @endif>
                                @for ($i = 0; $i < $item->depth + 1; $i++)
                                    |___
                                @endfor
                                {{ $item->title }}
                            </option>
                        @endforeach
                    </select>
                    <span>@lang('import.parent_hint')<br/></span>
                    <span class="text-danger">{{ $errors->first('parent') }}</span>
                </div>
                
                <div class="form-group">
                    <span>@lang('import.parent_item_type')</span>
                    <select name="parent_item_type" class="form-control" size=1 >
                        @foreach($item_types as $type)
                            <option value="{{$type->element_id}}"
                                @if(old('parent_item_type') == $type->element_id) selected @endif>
                                @foreach($type->values as $v)
                                    @if($v->attribute->name == 'name_'.app()->getLocale())
                                        {{$v->value}}
                                    @endif
                                @endforeach
                            </option>
                        @endforeach
                    </select>
                    <span>@lang('import.parent_item_type_hint')<br/></span>
                    <span class="text-danger">{{ $errors->first('parent_item_type') }}</span>
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
