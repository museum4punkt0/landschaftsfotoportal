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

                    <div id="map" class="map"><div id="popup"></div></div>
                    <script type="text/javascript">
                        var lon = {{ Config::get('geo.map_lon', 14.986) }};
                        var lat = {{ Config::get('geo.map_lat', 51.15) }};
                        var zoom = {{ Config::get('geo.map_zoom', 8) }};
                        
                        {{-- Init and display the map --}}
                        osm_map.display(lon, lat, zoom);
                        osm_map.addGeoJsonLayer('{{ route("map.all") }}');
                        
                        osm_map.addMarker(14.986789,  51.153432, '{{ asset("storage/images/logos/mein-smng.png") }}');
                        
                        var element = document.getElementById('popup');
                        
                        // Display popup on click
                        osm_map.map.on('click', function (evt) {
                            var feature = osm_map.map.forEachFeatureAtPixel(evt.pixel, function (feature) {
                                return feature;
                            });
                            if (feature) {
                                // Show popups for single items only, not for clusters
                                if (feature.get('features').length == 1) {
                                    // Destroy old popups
                                    $(element).popover('dispose');
                                    var coordinates = feature.getGeometry().getCoordinates();
                                    osm_map.popup.setPosition(coordinates);
                                    var ft = feature.get('features')[0];
                                    var content = '<a href="' + ft.get('details') + '">';
                                    content += '<img width=100 src="' + ft.get('preview') + '"/></a>';
                                    $(element).popover({
                                        placement: 'bottom',
                                        html: true,
                                        title: ft.get('name'),
                                        content: content,
                                    });
                                    $(element).popover('show');
                                }
                            } else {
                                $(element).popover('dispose');
                            }
                        });
                        
                        // Change mouse cursor when over marker
                        osm_map.map.on('pointermove', function (e) {
                            if (e.dragging) {
                                $(element).popover('dispose');
                                return;
                            }
                            var pixel = osm_map.map.getEventPixel(e.originalEvent);
                            var hit = osm_map.map.hasFeatureAtPixel(pixel);
                            osm_map.map.getTargetElement().style.cursor = hit ? 'pointer' : '';
                        });

                    </script>

{{-- Quick hack for LFP mock-up --}}
@if(Config::get('ui.frontend_layout') == 'landschaftsfotoportal')
                </div>
            </div>
        </div>
    </section>    
@endif

@endsection
