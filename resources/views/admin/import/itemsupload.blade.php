@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="card">
            <div class="card-header">@lang('import.header'): @lang('items.header')</div>
 
            <div class="card-body">
                <div class="card-text">
                    @lang('import.items_hint')
                </div>
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        @lang('import.upload_error')<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('import.items.save') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="itemTypeSelect">@lang('colmaps.item_type')</label>
                        <select id="itemTypeSelect" name="item_type" class="form-control" size=1 autofocus>
                            @foreach($item_types as $type)
                                <option value="{{$type->element_id}}"
                                    @if(old('item_type') == $type->element_id) selected @endif>
                                    @foreach($type->values as $v)
                                        @if($v->attribute->name == 'name_'.app()->getLocale())
                                            {{$v->value}}
                                        @endif
                                    @endforeach
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="fileInput">@lang('import.file')</label>
                        <input type="file" id="fileInput" name="fileUpload"
                            aria-describedby="fileInputHelpBlock" class="form-control-file"
                        >
                        <small id="fileInputHelpBlock" class="form-text text-muted">
                            @lang('import.file_hint')
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="columnSeparatorSelect">@lang('import.column_separator')</label>
                        <select id="columnSeparatorSelect" name="column_separator" class="form-control" size=1>
                            <option value=";" selected>;</option>
                            <option value=",">,</option>
                            <option value="|">|</option>
                            <option value="_">_</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="elementSeparatorSelect">@lang('import.element_separator')</label>
                        <select id="elementSeparatorSelect" name="element_separator" class="form-control" size=1>
                            <option value=";">;</option>
                            <option value="," selected>,</option>
                            <option value="|">|</option>
                            <option value="_">_</option>
                            <option value=" "> </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">@lang('common.upload')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
