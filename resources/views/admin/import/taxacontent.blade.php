@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">@lang('import.header'): @lang('taxon.header') ({{ $file_name }})</div>
        <div class="card-body">

            <form action="{{ route('import.taxa.process') }}" method="POST" class="form-horizontal">
                {{ csrf_field() }}

                <table class="table mx-0">
                    
                    @foreach ($csv_data as $row)
                        <tr>
                        @foreach ($row as $key => $value)
                            <td>{{ $value }}</td>
                        @endforeach
                        </tr>
                    @endforeach
                    <tr>
                    @foreach ($csv_data[0] as $key => $value)
                        <td>
                            <select name="fields[{{ $key }}]" style="width: 6em;">
                                <option value="0">@lang('common.ignore')</option>
                                <option value="taxon_name"
                                    @if(old('fields.'.$key) == 'taxon_name') selected @endif>
                                    @lang('taxon.taxon_name')
                                </option>
                                <option value="taxon_author"
                                    @if(old('fields.'.$key) == 'taxon_author') selected @endif>
                                    @lang('taxon.taxon_author')
                                </option>
                                <option value="taxon_suppl"
                                    @if(old('fields.'.$key) == 'taxon_suppl') selected @endif>
                                    @lang('taxon.taxon_suppl')
                                </option>
                                <option value="full_name"
                                    @if(old('fields.'.$key) == 'full_name') selected @endif>
                                    @lang('taxon.full_name')
                                </option>
                                <option value="native_name"
                                    @if(old('fields.'.$key) == 'native_name') selected @endif>
                                    @lang('taxon.native_name')
                                </option>
                                <option value="valid_name"
                                    @if(old('fields.'.$key) == 'valid_name') selected @endif>
                                    @lang('taxon.valid_name')
                                </option>
                                <option value="rank_abbr"
                                    @if(old('fields.'.$key) == 'rank_abbr') selected @endif>
                                    @lang('taxon.rank_abbr')
                                </option>
                                <option value="gsl_id"
                                    @if(old('fields.'.$key) == 'gsl_id') selected @endif>
                                    @lang('taxon.gsl_id')
                                </option>
                                <option value="bfn_namnr"
                                    @if(old('fields.'.$key) == 'bfn_namnr') selected @endif>
                                    @lang('taxon.bfn_namnr')
                                </option>
                                <option value="bfn_sipnr"
                                    @if(old('fields.'.$key) == 'bfn_sipnr') selected @endif>
                                    @lang('taxon.bfn_sipnr')
                                </option>
                                <option value="parent"
                                    @if(old('fields.'.$key) == 'parent') selected @endif>
                                    @lang('taxon.parent')
                                </option>
                            </select>
                        </td>
                    @endforeach
                    </tr>
                    <tr><td colspan={{ sizeof($csv_data[0]) }}>
                    @if ($errors->any())
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
                    @lang('import.fullname_hint')<br/>
                </span>
                </div>

                <div class="form-group">
                    <input type="checkbox" name="header" class="checkbox" value=1 @if(old('header')) checked @endif />
                    <span>@lang('import.contains_header')</span>
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
