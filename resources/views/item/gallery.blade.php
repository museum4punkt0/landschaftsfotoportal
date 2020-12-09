@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- Portfolio Grid, Gallery -->
    <section class="page-section bg-light" id="portfolio">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Neue Bilder</h2>
                <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
            </div>
            <div class="row">
            @foreach($items as $item)
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="portfolio-item">
                        <a class="portfolio-link d-flex justify-content-center" href="{{route('item.show.public', $item->item_id)}}">
                            <div class="portfolio-hover">
                                <div class="portfolio-hover-content text-center">
                                    <i class="portfolio-caption-heading">
                                    {{ $item->details->firstWhere('column_fk', 23)->value_string }}
                                    </i>
                                </div>
                            </div>
                            <img class="img-fluid" src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $item->details->firstWhere('column_fk', 13)->value_string .'.jpg') }}" alt="" />
                        </a>
                        <div class="portfolio-caption">
                            <div class="portfolio-caption-heading">
                                {{ $item->details->firstWhere('column_fk', 5)->value_string }}
                            </div>
                            <div class="portfolio-caption-subheading text-muted">
                                {{ $item->details->firstWhere('column_fk', 22)->value_string }},
                                {{ $item->details->firstWhere('column_fk', 20)->value_string }},
                                {{ $item->details->firstWhere('column_fk', 19)->value_string }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>

@endsection
