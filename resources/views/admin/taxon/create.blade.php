@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('taxon.new')</h2>

<form action="{{ route('taxon.store') }}" method="POST">
    
    <div class="form-group">
        <label for="taxonNameInput">@lang('taxon.taxon_name')</label>
        <input type="text" id="taxonNameInput" name="taxon_name" class="form-control"
            value="{{old('taxon_name')}}" maxlength="255" autofocus
        >
        <span class="text-danger">{{ $errors->first('taxon_name') }}</span>
    </div>
    <div class="form-group">
        <label for="taxonAuthorInput">@lang('taxon.taxon_author')</label>
        <input type="text" id="taxonAuthorInput" name="taxon_author" class="form-control"
            value="{{old('taxon_author')}}" maxlength="255"
        >
        <span class="text-danger">{{ $errors->first('taxon_author') }}</span>
    </div>
    <div class="form-group">
        <label for="taxonSupplInput">@lang('taxon.taxon_suppl')</label>
        <input type="text" id="taxonSupplInput" name="taxon_suppl" class="form-control"
            value="{{old('taxon_suppl')}}" maxlength="255"
        >
        <span class="text-danger">{{ $errors->first('taxon_suppl') }}</span>
    </div>
    <div class="form-group">
        <label for="fullNameInput">@lang('taxon.full_name')</label>
        <input type="text" id="fullNameInput" name="full_name" class="form-control"
            value="{{old('full_name')}}" maxlength="255"
        >
        <span class="text-danger">{{ $errors->first('full_name') }}</span>
    </div>
    <div class="form-group">
        <label for="nativeNameInput">@lang('taxon.native_name')</label>
        <input type="text" id="nativeNameInput" name="native_name" class="form-control"
            value="{{old('native_name')}}" maxlength="255"
        >
        <span class="text-danger">{{ $errors->first('native_name') }}</span>
    </div>

    @include('includes.form_taxon_autocomplete', [
        'search_url' => route('taxon.autocomplete', ['valid' => true]),
        'div_class' => 'form-group',
        'name' => 'valid_name',
        'input_placeholder' => '',
        'input_label' => __('taxon.valid_name'),
        'null_label' => __('taxon.valid'),
        'taxon_name' => old('valid_name_name', __('taxon.valid')),
        'taxon_id' => old('valid_name'),
    ])
    @include('includes.form_taxon_autocomplete', [
        'search_url' => route('taxon.autocomplete', ['valid' => true]),
        'div_class' => 'form-group',
        'name' => 'parent',
        'input_placeholder' => '',
        'input_label' => __('taxon.parent'),
        'null_label' => __('common.root'),
        'taxon_name' => old('parent_name', __('common.root')),
        'taxon_id' => old('parent'),
    ])

    <div class="form-group">
        <label for="rankAbbrInput">@lang('taxon.rank_abbr')</label>
        <input type="text" id="rankAbbrInput" name="rank_abbr" class="form-control"
            value="{{old('rank_abbr')}}" maxlength="255"
        >
        <span class="text-danger">{{ $errors->first('rank_abbr') }}</span>
    </div>
    <div class="form-group">
        <label for="glsIdInput">@lang('taxon.gsl_id')</label>
        <input type="text" id="glsIdInput" name="gsl_id" class="form-control"
            value="{{old('gsl_id')}}" maxlength="10"
        >
        <span class="text-danger">{{ $errors->first('gsl_id') }}</span>
    </div>
    <div class="form-group">
        <label for="BfnNamnrInput">@lang('taxon.bfn_namnr')</label>
        <input type="text" id="BfnNamnrInput" name="bfn_namnr" class="form-control"
            value="{{old('bfn_namnr')}}" maxlength="10"
        >
        <span class="text-danger">{{ $errors->first('bfn_namnr') }}</span>
    </div>
    <div class="form-group">
        <label for="bfnSipnrInput">@lang('taxon.bfn_sipnr')</label>
        <input type="text" id="bfnSipnrInput" name="bfn_sipnr" class="form-control"
            value="{{old('bfn_sipnr')}}" maxlength="10"
        >
        <span class="text-danger">{{ $errors->first('bfn_sipnr') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
