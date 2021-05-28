@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="card">
            <div class="card-header">@lang('import.header'): @lang('lists.header')</div>
 
            <div class="card-body">
                <div class="card-text">
                    @lang('import.lists_hint')
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

                <form action="{{ route('import.csv.save') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <span>@lang('lists.list')</span>
                        <select name="list">
                        @foreach ($lists as $list)
                            <option value="{{ $list->list_id }}">
                                {{ $list->name }} ({{ $list->description }})
                            </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control-file" name="fileUpload">
                        <small class="form-text text-muted">@lang('import.file_hint')</small>
                    </div>
                    <div class="form-group">
                        <label for="column_separator">@lang('import.column_separator')</label>
                        <select name="column_separator" id="column_separator" class="form-control" size=1 >
                            <option value=";" selected>;</option>
                            <option value=",">,</option>
                            <option value="|">|</option>
                            <option value="_">_</option>
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
