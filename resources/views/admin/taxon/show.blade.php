@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')
@include('includes.modal_image_large')

<div class="container">
    <h2>@lang('taxon.list')</h2>
    <div class="my-4">
        <a href="{{ route('taxon.edit', $taxon) }}" class="btn btn-primary">
            @lang('common.edit')
        </a>
    </div>

    <!--
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('taxon.full_name')</h5>
        </div>
        <div class="card card-body">
            {{ $taxon->full_name }}
        </div>
    </div>
    -->

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('common.name')</h5>
        </div>
        <div class="card card-body">
            <table class="table table-sm table-borderless">
                <tr>
                    <td>@lang('taxon.full_name')</td><td>{{ $taxon->full_name }}</td>
                </tr>
                <tr>
                    <td>@lang('taxon.taxon_name')</td><td>{{ $taxon->taxon_name }}</td>
                </tr>
                <tr>
                    <td>@lang('taxon.taxon_author')</td><td>{{ $taxon->taxon_author }}</td>
                </tr>
                <tr>
                    <td>@lang('taxon.taxon_suppl')</td><td>{{ $taxon->taxon_suppl }}</td>
                </tr>
                <tr>
                    <td>@lang('taxon.native_name')</td><td>{{ $taxon->native_name }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('taxon.synonyms')</h5>
        </div>
        <div class="card card-body">
        @foreach ($taxon->synonyms as $synonym)
            <a href="{{ route('taxon.show', $synonym->taxon_id) }}">
                <i class="fas {{ Config::get('ui.icon_permalink', 'fa-link') }}"
                    title="@lang('items.related_item')"></i>
                {{ $synonym->full_name }}
            </a>
            <br>
        @endforeach
        @if ($taxon->valid_name)
            @lang('taxon.valid_name'): 
            <a href="{{ route('taxon.show', $taxon->valid_taxon->taxon_id) }}">
                <i class="fas {{ Config::get('ui.icon_permalink', 'fa-link') }}"
                    title="@lang('items.related_item')"></i>
                {{ $taxon->valid_taxon->full_name }}
            </a>
        @else
            @lang('taxon.valid')
        @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('taxon.rank_abbr')</h5>
        </div>
        <div class="card card-body">
            {{ $taxon->rank_abbr }}
        </div>
    </div>

    <div class="card" id="anchestors">
        <div class="card-header">
            <h5 class="mb-0">@lang('taxon.anchestors')</h5>
        </div>
        <div class="card card-body">
            <div class="d-inline d-print-none mb-3">
                <div class="btn-group btn-group-sm" role="group" aria-labelledby="anchestorsRanksLabel">
                    <a class="btn btn-secondary" href="?anchestors=5#anchestors">5</a>
                    <a class="btn btn-secondary" href="?anchestors=10#anchestors">10</a>
                    <a class="btn btn-secondary" href="?anchestors=25#anchestors">25</a>
                </div>
                <label id="anchestorsRanksLabel">@lang('taxon.anchestors_ranks')</label>
            </div>
        @foreach ($anchestors as $rank => $anchestor)
            <a href="{{ route('taxon.show', $anchestor->taxon_id) }}">
                <i class="fas {{ Config::get('ui.icon_permalink', 'fa-link') }}"
                    title="@lang('items.related_item')"></i>
                @for ($i = 0; $i < $rank; $i++)---@endfor
                {{ $anchestor->full_name }} ({{ $anchestor->rank_abbr }})
            </a>
        @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('lists.children')</h5>
        </div>
        <div class="card card-body">
        @foreach ($taxon->children as $child)
            <a href="{{ route('taxon.show', $child->taxon_id) }}">
                <i class="fas {{ Config::get('ui.icon_permalink', 'fa-link') }}"
                    title="@lang('items.related_item')"></i>
                {{ $child->full_name }} ({{ $child->rank_abbr }})
            </a>
            <br>
        @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('taxon.related_items')</h5>
        </div>
        <div class="card card-body">
        @foreach ($taxon->items->sortBy('item_type_fk') as $item)
            <a href="{{ route('item.show', $item) }}">
                <i class="fas {{ Config::get('ui.icon_permalink', 'fa-link') }}"
                    title="@lang('items.related_item')"></i>
                {{ $item->title }} ({{ optional($item_types->firstWhere('element_fk', $item->item_type_fk))->value }})
            </a>
            <br>
        @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('common.id')</h5>
        </div>
        <div class="card card-body">
            <table class="table table-sm table-borderless">
                <tr>
                    <td>@lang('taxon.bfn_sipnr')</td>
                    <td>
                    @if ($taxon->bfn_sipnr)
                        <a target="_blank" href="https://floraweb.de/xsql/artenhome.xsql?suchnr={{ $taxon->bfn_sipnr }}">
                            <i class="fas {{ Config::get('ui.icon_external_link', 'fa-external-link-alt') }}"
                                title="@lang('items.related_item')"></i>
                            {{ $taxon->bfn_sipnr }}
                        </a>
                    @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('taxon.bfn_namnr')</td><td>{{ $taxon->bfn_namnr }}</td>
                </tr>
                <tr>
                    <td>@lang('taxon.gsl_id')</td><td>{{ $taxon->gsl_id }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if(env('APP_DEBUG'))
        [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
    @endif

</div>

@endsection
