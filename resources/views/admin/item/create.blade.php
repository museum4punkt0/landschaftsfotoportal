@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('items.new')</h2>

@if(count($colmap)==0)
    <div class="alert alert-info">
        @lang('colmaps.none_available')
    </div>
    <div>
        <a href="{{route('colmap.create')}}" class="btn btn-primary">@lang('colmaps.new')</a>
    </div>
@else

<form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
    
    <div class="form-group">
        <span>@lang('items.menu_title')</span>
        <input type="text" name="title" class="form-control" value="{{old('title')}}" />
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.published')</span>
        <select name="public" class="form-control" size=1 >
            <option value="1"
                @if(old('public') == 1) selected @endif>
                @lang('common.yes')
            </option>
            <option value="0"
                @if(old('public') == 0) selected @endif>
                @lang('common.no')
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.parent')</span>
        <select name="parent" class="form-control" size=1 >
            <option value="">@lang('common.root')</option>
            @foreach($items as $item)
                <option value="{{$item->item_id}}"
                    @if(old('parent') == $item->item_id) selected @endif>
                    @for ($i = 0; $i < $item->depth + 1; $i++)
                        |___
                    @endfor
                    {{ $item->title }}
                    ({{ $item->item_type_fk }})
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.list')</span>
        <select name="taxon" class="form-control" size=1 readonly >
            <option value="">@lang('common.none')</option>
            @foreach($taxa as $taxon)
                @unless($taxon->valid_name)
                    <option value="{{$taxon->taxon_id}}"
                        @if(old('taxon', session('taxon')) == $taxon->taxon_id) selected @endif>
                        @for ($i = 0; $i < $taxon->depth; $i++)
                            |___
                        @endfor
                        {{$taxon->taxon_name}} {{$taxon->taxon_author}} ({{$taxon->native_name}})
                    </option>
                @endunless
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('taxon') }}</span>
    </div>
    
    @foreach($colmap as $cm)
        
        @switch($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value)
            
            {{-- Data_type of form field is list --}}
            @case('_list_')
                {{-- dd($lists->firstWhere('list_id', $cm->column->list_fk)->elements) --}}
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <select name="fields[{{ $cm->column->column_id }}]" class="form-control" size=1 >
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(old('fields.'. $cm->column->column_id) == $element->element_id)
                                    selected
                                @endif
                            >
                                @for ($i = 0; $i < $element->depth; $i++)
                                    |___
                                @endfor
                                @foreach($element->values as $v)
                                    {{$v->value}}, 
                                @endforeach
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
            {{-- Data_type of form field is image pixel per inch --}}
            @case('_image_ppi_')
            {{-- Data_type of form field is float --}}
            @case('_float_')
            {{-- Data_type of form field is string --}}
            @case('_string_')
            {{-- Data_type of form field is (menu) title --}}
            @case('_title_')
            {{-- Data_type of form field is image title --}}
            @case('_image_title_')
            {{-- Data_type of form field is image copyright --}}
            @case('_image_copyright_')
            {{-- Data_type of form field is redirect --}}
            @case('_redirect_')
            {{-- Data_type of form field is map --}}
            @case('_map_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{old('fields.'. $cm->column->column_id)}}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is html --}}
            @case('_html_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <textarea name="fields[{{ $cm->column->column_id }}]" class="form-control summernote" 
                        rows=5>{!! old('fields.'. $cm->column->column_id) !!}</textarea>
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('.summernote').summernote({
                            tabsize: 4,
                            height: 200
                        });
                    });
                </script>
                @break
            
            {{-- Data_type of form field is date --}}
            @case('_date_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="date" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{old('fields.'. $cm->column->column_id)}}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="url" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{old('fields.'. $cm->column->column_id, 'https://')}}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is image --}}
            @case('_image_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="file" class="form-control-file" name="fields[{{ $cm->column->column_id }}]" />
                    <span class="form-text text-muted">@lang('column.image_hint')</span>
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
        @endswitch
        
    @endforeach
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

@if(env('APP_DEBUG'))
    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
@endif

@endif

</div>

@endsection
