@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- Timeline -->
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'timeline',
        'section_heading' => __(config('ui.frontend_layout') . '.timeline_heading'),
        'section_subheading' => __(config('ui.frontend_layout') . '.timeline_subheading'),
    ])
    
            <ul class="timeline">
            @foreach($decades as $decade => $count)
                @if($loop->even)
                    <li class="timeline-inverted">
                @else
                    <li>
                @endif
                
                    <a href="{{ route('search.index',
                        ['fields['. ($image_module->config['columns']['daterange'] ?? 0) .']' => $decade]) }}#searchResults">
                    <div class="timeline-image">
                        <h4>
                        @if($decade > 0)
                            {{ $decade }}@lang('common.decade_suffix')
                        @else
                            @lang('common.unknown')
                        @endif
                        </h4>
                        <p>
                            {{ $count }} @lang(config('ui.frontend_layout') . '.items')
                        </p>
                    </div>
                    </a>
                    <div class="timeline-panel">
                        <div class="timeline-body">
                            <div class="row">
                            @foreach($items[$decade] as $item)
                                <a href="{{ route('item.show.public', $item->item_id) }}">
                                    <div class="timelinethumb" style="background-image: url({{ asset('storage/'. Config::get('media.preview_dir') . $item->details->firstWhere('column_fk', $image_module->config['columns']['filename'] ?? 0)->value_string) }});"></div>
                                </a>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
            
            </ul>
    
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')
    
@endsection
