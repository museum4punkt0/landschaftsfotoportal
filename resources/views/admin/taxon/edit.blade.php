@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('taxon.edit')</h2>

<form action="{{ route('taxon.update', $taxon->taxon_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('taxon.name')</span>
        <input type="text" name="taxon_name" class="form-control" value="{{old('taxon_name', $taxon->taxon_name)}}" />
        <span class="text-danger">{{ $errors->first('taxon_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.author')</span>
        <input type="text" name="taxon_author" class="form-control" value="{{old('taxon_author', $taxon->taxon_author)}}" />
        <span class="text-danger">{{ $errors->first('taxon_author') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.native_name')</span>
        <input type="text" name="native_name" class="form-control" value="{{old('native_name', $taxon->native_name)}}" />
        <span class="text-danger">{{ $errors->first('native_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.valid_name')</span>
        <select name="valid_name" class="form-control" size=1 >
            <option value="">@lang('taxon.valid')</option>
            @foreach($taxa as $t)
                @unless($t->valid_name)
                    <option value="{{$t->taxon_id}}"
                        @if(old('valid_name', $taxon->valid_name) == $t->taxon_id) selected @endif>
                        @for ($i = 0; $i < $t->depth; $i++)
                            |___
                        @endfor
                        {{$t->taxon_name}} {{$t->taxon_author}} ({{$t->native_name}})
                    </option>
                @endunless
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('valid_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.parent')</span>
        <select name="parent" class="form-control" size=1 >
            <option value="">@lang('common.root')</option>
            @foreach($taxa as $t)
                @unless($t->valid_name)
                    <option value="{{$t->taxon_id}}"
                        @if(old('parent', $taxon->parent_fk) == $t->taxon_id) selected @endif>
                        @for ($i = 0; $i < $t->depth; $i++)
                            |___
                        @endfor
                        {{$t->taxon_name}} {{$t->taxon_author}} ({{$t->native_name}})
                    </option>
                @endunless
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.gsl_id')</span>
        <input type="text" name="gsl_id" class="form-control" value="{{old('gsl_id', $taxon->gsl_id)}}" />
        <span class="text-danger">{{ $errors->first('gsl_id') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@endsection
