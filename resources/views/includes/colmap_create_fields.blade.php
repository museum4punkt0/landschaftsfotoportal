    <div class="form-group">
        <label for="columnGroupSelect">@lang('columns.column_group')</label>
        <select id="columnGroupSelect" name="column_group" class="form-control" size=1 >
            @foreach($column_groups as $group)
                <option value="{{$group->element_fk}}"
                    @if(old('column_group') == $group->element_fk) selected @endif>
                    {{$group->value}}
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('column_group') }}</span>
    </div>
    <div class="form-group">
        <label for="itemTypeSelect">@lang('colmaps.item_type')</label>
        <select id="itemTypeSelect" name="item_type" class="form-control" size=1 >
            @foreach($item_types as $type)
                <option value="{{$type->element_fk}}"
                    @if(old('item_type') == $type->element_fk) selected @endif>
                    {{$type->value}}
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('item_type') }}</span>
    </div>
    @include('includes.form_taxon_autocomplete', [
        'search_url' => route('taxon.autocomplete', ['valid' => true]),
        'div_class' => 'form-group',
        'name' => 'taxon',
        'input_placeholder' => '',
        'input_label' => __('taxon.list'),
        'null_label' => __('common.all'),
        'taxon_name' => old('taxon_name', __('common.all')),
        'taxon_id' => old('taxon'),
    ])
    <div class="form-group">
        <label for="publicSelect">@lang('common.published')</label>
        <select id="publicSelect" name="public" class="form-control" size=1 >
            <option value="1"
                @if(old('public') == 1) selected @endif>
                @lang('common.yes')
            </option>
            <option value="0"
                @if(old('public') == 0) selected @endif>
                @lang('common.no')
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('public') }}</span>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" id="sortEndCheckbox" name="sort_end" aria-describedby="sortEndHelpBlock"
                class="form-check-input" value=1 checked
            >
            <label for="sortEndCheckbox" class="form-check-label">
                @lang('colmaps.sort_end')
            </label>
            <small id="sortEndHelpBlock" class="form-text text-muted">
                @lang('colmaps.sort_end_help')
            </small>
        </div>
    </div>
    <div class="form-group">
        <label for="apiAttributeInput">@lang('colmaps.api_attribute')</label>
        <input type="text" id="apiAttributeInput" name="api_attribute" class="form-control"
            value="{{old('api_attribute')}}" maxlength="255"
        />
        <span class="text-danger">{{ $errors->first('api_attribute') }}</span>
    </div>
    <div class="form-group">
        <label for="configInput">@lang('colmaps.config')</label>
        <input type="text" id="configInput" name="config" class="form-control"
            value="{{old('config')}}" maxlength="4095"
        />
        <span class="text-danger">{{ $errors->first('config') }}</span>
    </div>
