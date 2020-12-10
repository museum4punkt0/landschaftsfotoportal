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
                        <div class="timeline-body"><p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sunt ut voluptatum eius sapiente, totam reiciendis temporibus qui quibusdam, recusandae sit vero unde, sed, incidunt et ea quo dolore laudantium consectetur!
                        </p></div>
                    </div>
                </li>
            @endforeach
            
            </ul>
        </div>
    </section>

@endsection
