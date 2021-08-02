@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- Map -->
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'big_map',
        'section_heading' => 'Karte',
        'section_subheading' => 'Lorem ipsum dolor sit amet consectetur.',
    ])
    
                    <div id="map" class="map"
                        data-column-lat={{ $column_ids['lat']}}
                        data-column-lon={{ $column_ids['lon']}}
                        data-ajax-url={{ $options['ajax_url']}}
                    >
                        <div id="popup"></div>
                    </div>
                    <script type="text/javascript">
                        // Default values, used if geolocation API fails or is disabled
                        var lon = {{ Config::get('geo.map_lon', 14.986) }};
                        var lat = {{ Config::get('geo.map_lat', 51.15) }};
                        var zoom = {{ Config::get('geo.map_zoom', 8) }};
                        
                        // Use browser's geolocation API if available and enabled in config
                        if (Boolean({{ Config::get('geo.use_geolocation', false) }}) && navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                lon = position.coords.longitude;
                                lat = position.coords.latitude;
                                initMap();
                            }, function (error) {
                                initMap();
                            }, {
                                timeout:2500
                            });
                        }
                        else {
                            initMap();
                        }
                        
                        {{-- Init and display the map --}}
                        function initMap() {
                            var element = $('#popup');
                            var ajaxUrl = $('#map').data('ajax-url');
                            var columnLat = $('#map').data('column-lat');
                            var columnLon = $('#map').data('column-lon');
                            
                            osm_map.display(lon, lat, zoom);
                            osm_map.addGeoJsonLayer(ajaxUrl);
                            
                            osm_map.addMarker(14.986789,  51.153432, '{{ asset("storage/images/logos/mein-smng.png") }}');
                            
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
                                    else {
                                        var extent = osm_map.getExtendOfFeatures(feature.get('features'));
                                        //console.log(extent);
                                        
                                        // Destroy old popups
                                        $(element).popover('dispose');
                                        var coordinates = feature.getGeometry().getCoordinates();
                                        osm_map.popup.setPosition(coordinates);
                                        var content = '<a href="{{ route("search.index") }}';
                                        content += '?fields[' + columnLon + '][min]=' + extent[0];
                                        content += '&fields[' + columnLon + '][max]=' + extent[2];
                                        content += '&fields[' + columnLat + '][min]=' + extent[1];
                                        content += '&fields[' + columnLat + '][max]=' + extent[3];
                                        content += '">@lang("common.showall")</a>';
                                        $(element).popover({
                                            placement: 'bottom',
                                            html: true,
                                            title: feature.get('features').length + ' @lang("items.header")',
                                            content: content,
                                        });
                                        $(element).popover('show');
                                    }
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
                        }

                    </script>
    
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')

@endsection
