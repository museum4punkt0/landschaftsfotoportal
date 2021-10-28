@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('taxon.edit')</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        @lang('common.form_validation_error')
    </div>
@endif

<form action="{{ route('taxon.update', $taxon->taxon_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('taxon.taxon_name')</span>
        <input type="text" name="taxon_name" class="form-control" value="{{old('taxon_name', $taxon->taxon_name)}}" />
        <span class="text-danger">{{ $errors->first('taxon_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.taxon_author')</span>
        <input type="text" name="taxon_author" class="form-control" value="{{old('taxon_author', $taxon->taxon_author)}}" />
        <span class="text-danger">{{ $errors->first('taxon_author') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.taxon_suppl')</span>
        <input type="text" name="taxon_suppl" class="form-control" value="{{old('taxon_suppl', $taxon->taxon_suppl)}}" />
        <span class="text-danger">{{ $errors->first('taxon_suppl') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.full_name')</span>
        <input type="text" name="full_name" class="form-control" value="{{old('full_name', $taxon->full_name)}}" />
        <span class="text-danger">{{ $errors->first('full_name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.native_name')</span>
        <input type="text" name="native_name" class="form-control" value="{{old('native_name', $taxon->native_name)}}" />
        <span class="text-danger">{{ $errors->first('native_name') }}</span>
    </div>

    @include('includes.form_taxon_autocomplete', [
        'search_url' => route('taxon.autocomplete', ['valid' => true]),
        'div_class' => 'form-group',
        'name' => 'valid_name',
        'input_placeholder' => '',
        'input_label' => __('taxon.valid_name'),
        'null_label' => __('taxon.valid'),
        'taxon_name' => old('valid_name_name', optional($taxon->valid_taxon)->full_name ?? __('taxon.valid')),
        'taxon_id' => old('valid_name', $taxon->valid_name),
    ])
    @include('includes.form_taxon_autocomplete', [
        'search_url' => route('taxon.autocomplete', ['valid' => true]),
        'div_class' => 'form-group',
        'name' => 'parent',
        'input_placeholder' => '',
        'input_label' => __('taxon.parent'),
        'null_label' => __('common.root'),
        'taxon_name' => old('parent_name', optional($taxon->parent)->full_name ?? __('common.root')),
        'taxon_id' => old('parent', $taxon->parent_fk),
    ])

    <div class="form-group">
        <span>@lang('taxon.rank_abbr')</span>
        <input type="text" name="rank_abbr" class="form-control" value="{{old('rank_abbr', $taxon->rank_abbr)}}" />
        <span class="text-danger">{{ $errors->first('rank_abbr') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.gsl_id')</span>
        <input type="text" name="gsl_id" class="form-control" value="{{old('gsl_id', $taxon->gsl_id)}}" />
        <span class="text-danger">{{ $errors->first('gsl_id') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.bfn_namnr')</span>
        <input type="text" name="bfn_namnr" class="form-control" value="{{old('bfn_namnr', $taxon->bfn_namnr)}}" />
        <span class="text-danger">{{ $errors->first('bfn_namnr') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.bfn_sipnr')</span>
        <input type="text" name="bfn_sipnr" class="form-control" value="{{old('bfn_sipnr', $taxon->bfn_sipnr)}}" />
        <span class="text-danger">{{ $errors->first('bfn_sipnr') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@endsection
