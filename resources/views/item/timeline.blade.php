@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- Timeline -->
    <section class="page-section" id="timeline">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Zeitstrahl</h2>
                <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
            </div>
            <ul class="timeline">
            @foreach($decades as $dec)
                @if($loop->even)
                    <li class="timeline-inverted">
                @else
                    <li>
                @endif
                
                    <div class="timeline-image">
                        <h4>{{ $dec->decade }}er</h4>
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4 class="subheading">{{ $dec->images_count}} Fotos</h4>
                        </div>
                        <div class="timeline-body">
                            <div class="row"><p class="text-muted">
                            @foreach($details[$dec->decade] as $detail)
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $detail->item->details->firstWhere('column_fk', 13)->value_string .'.jpg') }}" height=100 alt="" />
                            @endforeach
                            </p></div>
                        </div>
                    </div>
                </li>
            @endforeach
            
            </ul>
        </div>
    </section>

@endsection
