@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

{{-- Quick hack for LFP mock-up --}}
@if(Config::get('ui.frontend_layout') == 'landschaftsfotoportal')

    <!-- Map -->
    <section class="page-section" id="big_map">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Karte</h2>
                <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
            </div>
            <div class="card">
                <div class="card-body">
@endif

                    <div id="map" class="map"></div>
                    <script type="text/javascript">
                        var lon = 14.5;
                        var lat = 51;
                        var zoom = 10;
                        
                        {{-- Init and display the map --}}
                        osm_map.display(lon, lat, zoom);
                        osm_map.addGeoJsonLayer('{{ route("map.all") }}');
                        
                        osm_map.addMarker(14.986789,  51.153432, '{{ asset("storage/images/logos/mein-smng.png") }}');
                    </script>

{{-- Quick hack for LFP mock-up --}}
@if(Config::get('ui.frontend_layout') == 'landschaftsfotoportal')
                </div>
            </div>
        </div>
    </section>    
@endif

@endsection
