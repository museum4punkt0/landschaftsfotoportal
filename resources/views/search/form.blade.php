@extends('layouts.frontend')

@section('sidebar_menu_items')
    @parent
    
    @foreach($menu_root as $it)
        <li class="nav-item">
            <a class="nav-link" href="{{ route('item.show.public', [$it->item_id]) }}">
                {{ $it->title }}
            </a>
        </li>
    @endforeach
    
@endsection

@section('content')

<div class="container">
<h2>@lang('search.header')</h2>

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    
    <form action="{{ route('search.results') }}" method="POST">
        <div class="form-group">
            <span>@lang('search.taxon_name')</span>
            <input type="text" name="taxon_name" class="form-control" value="{{old('taxon_name')}}" />
            <span class="text-danger">{{ $errors->first('taxon_name') }}</span>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-secondary">@lang('search.search')</button>
        </div>
        {{ csrf_field() }}
        @method('POST')
    </form>
    
    @isset($taxa)
        <h4>@lang('search.results')</h4>
        <ul class="list-group">
        @foreach($taxa as $taxon)
            <li class="list-group-item">
            @auth
                <a href="{{ route('taxon.edit', [$taxon->taxon_id]) }}" target="_blank">
            @endauth
                    {{ $taxon->full_name }}
            @auth
                </a>
            @endauth
                &nbsp;
                <span class="badge badge-secondary">{{ count($taxon->items )}}</span>
                @if(count($taxon->items))
                    <ul class="list-group">
                    @foreach($taxon->items->sortBy('item_type_fk')->sortBy('title') as $item)
                        <li class="list-group-item">
                            <a href="{{ route('item.show.public', [$item->item_id]) }}">
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
        </ul>
    @endisset
    
    @if(env('APP_DEBUG'))
        [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
    @endif

</div>

@endsection
