@extends('layouts.app')

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container">
    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('taxon.header')</div>
            <div class="card-body">
                <a href="{{route('taxon.create')}}" class="btn btn-primary">@lang('taxon.new')</a>
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('taxon.taxon_name')</th>
                        <th colspan="1">@lang('taxon.native_name')</th>
                        <th colspan="1">@lang('taxon.valid_name')</th>
                        <th colspan="1">@lang('taxon.rank')</th>
                        <th colspan="1">@lang('taxon.gsl_id')</th>
                        <th colspan="2">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($taxa as $taxon)
                    <tr>
                        <td>
                            {{$taxon->taxon_id}}
                        </td>
                        <td>
                    @for ($i = 0; $i < $taxon->depth; $i++)
                        |___
                    @endfor
                            {{$taxon->taxon_name}} {{$taxon->taxon_author}} {{$taxon->taxon_suppl}}
                        </td>
                        <td>
                            {{$taxon->native_name}}<br/>
                        </td>
                        <td>
                            @if($taxon->valid_name)
                                ID {{$taxon->valid_name}}
                            @else
                                @lang('common.yes')
                            @endif
                        </td>
                        <td>
                            {{$taxon->rank_abbr}}<br/>
                        </td>
                        <td>
                            {{$taxon->gsl_id}}<br/>
                        </td>
                        <td>
                            <form action="{{route('taxon.edit', $taxon)}}" method="GET">
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('taxon.destroy', $taxon)}}" method="POST">
                                {{ csrf_field() }}
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">@lang('common.delete')</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <h3>You need to log in. <a href="{{url()->current()}}/login">Click here to login</a></h3>
            </div>
        @endif
        <div class="card-footer">
            {{ $taxa->links() }}
        </div>
    </div>
</div>

@endsection
