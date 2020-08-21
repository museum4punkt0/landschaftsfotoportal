@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('items.edit')</h2>

<form action="{{ route('item.update', $item->item_id) }}" method="POST" enctype="multipart/form-data">
    
    <div class="form-group">
        <span>@lang('items.menu_title')</span>
        <input type="text" name="title" class="form-control" value="{{old('title', $item->title)}}" />
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.parent')</span>
        <select name="parent" class="form-control" size=1 >
            <option value="">@lang('common.root')</option>
            @foreach($items as $it)
                <option value="{{$it->item_id}}"
                    @if(old('parent', $item->parent_fk) == $it->item_id) selected @endif>
                    @for ($i = 0; $i < $it->depth + 1; $i++)
                        |___
                    @endfor
                    {{ $it->title }}
                    ({{ $it->item_type_fk }})
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.list')</span>
        <select name="taxon" id="taxon_select" class="form-control" size=1 readonly>
            <option value="">@lang('common.none')</option>
            @foreach($taxa as $taxon)
                @unless($taxon->valid_name)
                    <option value="{{$taxon->taxon_id}}"
                        @if(old('taxon', $item->taxon_fk) == $taxon->taxon_id) selected @endif>
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
    <script type="text/javascript">
        var elem = document.getElementById("taxon_select");
        elem.addEventListener("change", TaxonChanged);

        function TaxonChanged() {
            var tax = document.getElementById("taxon_select").selectedIndex;
            alert('Changing the Taxon is not allowed!');
            //window.location.reload(true);
        }
    </script>
    
    @foreach($colmap as $cm)
        @switch($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value)
            
            {{-- Data_type of form field is list --}}
            @case('_list_')
                {{-- dd($lists->firstWhere('list_id', $cm->column->list_fk)->elements) --}}
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <select name="fields[{{ $cm->column->column_id }}]" class="form-control" size=1 >
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(old('fields.'. $cm->column->column_id, 
                                    $details->firstWhere('column_fk', $cm->column->column_id)->element_fk) == 
                                     $element->element_id)
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
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_int) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is float --}}
            @case('_float_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_float) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is date --}}
            @case('_date_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="date" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
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
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }}
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    @if($details->firstWhere('column_fk', $cm->column->column_id))
                        <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                            value="{{ old('fields.'. $cm->column->column_id, 
                            $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}" />
                    @else
                        <span>detail column {{$cm->column->column_id}} for map not found</span>
                    @endif
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is html --}}
            @case('_html_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <textarea name="fields[{{ $cm->column->column_id }}]" class="form-control summernote" 
                        rows=5>{!! old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) !!}</textarea>
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
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="url" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is image --}}
            @case('_image_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <div class="form-row">
                        <div class="col">
                            <input type="file" class="form-control-file" name="fields[{{ $cm->column->column_id }}]" />
                            <span class="form-text text-muted">@lang('column.image_hint')</span>
                        </div>
                        <div class="col">
                        @if($cm->getConfigValue('image_show') == 'preview')
                            @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string))
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                                    width=100
                                />
                            @else
                                @lang('columns.image_not_available')
                            @endif
                        @endif
                        </div>
                    </div>
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
    @method('PATCH')
</form>

@if(env('APP_DEBUG'))
    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
@endif

</div>

@endsection
