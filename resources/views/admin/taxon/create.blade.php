@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('taxon.new')</h2>

<form action="{{ route('taxon.store') }}" method="POST">
    
    <div class="form-group">
        <span>@lang('taxon.taxon_name')</span>
        <input type="text" name="taxon_name" class="form-control" value="{{old('taxon_name')}}" />
        <span class="text-danger">{{ $errors->first('taxon_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.taxon_author')</span>
        <input type="text" name="taxon_author" class="form-control" value="{{old('taxon_author')}}" />
        <span class="text-danger">{{ $errors->first('taxon_author') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.taxon_suppl')</span>
        <input type="text" name="taxon_suppl" class="form-control" value="{{old('taxon_suppl')}}" />
        <span class="text-danger">{{ $errors->first('taxon_suppl') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.full_name')</span>
        <input type="text" name="full_name" class="form-control" value="{{old('full_name')}}" />
        <span class="text-danger">{{ $errors->first('full_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.native_name')</span>
        <input type="text" name="native_name" class="form-control" value="{{old('native_name')}}" />
        <span class="text-danger">{{ $errors->first('native_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.valid_name')</span>
        <select name="valid_name" class="form-control" size=1 >
            <option value="">@lang('taxon.valid')</option>
            @foreach($taxa as $taxon)
                @unless($taxon->valid_name)
                    <option value="{{$taxon->taxon_id}}"
                        @if(old('valid_name') == $taxon->taxon_id) selected @endif>
                        @for ($i = 0; $i < $taxon->depth; $i++)
                            |___
                        @endfor
                        {{$taxon->taxon_name}} {{$taxon->taxon_author}} ({{$taxon->native_name}})
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
            @foreach($taxa as $taxon)
                @unless($taxon->valid_name)
                    <option value="{{$taxon->taxon_id}}"
                        @if(old('parent') == $taxon->taxon_id) selected @endif>
                        @for ($i = 0; $i < $taxon->depth; $i++)
                            |___
                        @endfor
                        {{$taxon->taxon_name}} {{$taxon->taxon_author}} ({{$taxon->native_name}})
                    </option>
                @endunless
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.rank_abbr')</span>
        <input type="text" name="rank_abbr" class="form-control" value="{{old('rank_abbr')}}" />
        <span class="text-danger">{{ $errors->first('rank_abbr') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.gsl_id')</span>
        <input type="text" name="gsl_id" class="form-control" value="{{old('gsl_id')}}" />
        <span class="text-danger">{{ $errors->first('gsl_id') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
