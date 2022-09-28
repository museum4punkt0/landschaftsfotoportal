import {Map, Feature, View, Overlay} from 'ol';
import {ScaleLine} from 'ol/control';
import MousePosition from 'ol/control/MousePosition';
import {createStringXY} from 'ol/coordinate';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import ClusterSource from 'ol/source/Cluster';
import TileWMS from 'ol/source/TileWMS';
import GeoJSON from 'ol/format/GeoJSON';
import {Icon, Style, Circle as CircleStyle, Fill, Stroke, Text} from 'ol/style';
import * as olExtent from 'ol/extent';
import Point from 'ol/geom/Point';
import OSM from 'ol/source/OSM';
import {fromLonLat, toLonLat, transform, transformExtent} from 'ol/proj';

var osm_map = {
    map: false,
    config: false,
    owner: new Object,
    popup: false,
    
    vectorLayer: new VectorLayer({
        source: new VectorSource({
            features: false,
        })
    }),
    
    geoJsonLayer: false,
    
    // Init
    init: function (colmapId, itemId, configUrl) {
        this.owner.colmapId = colmapId;
        this.owner.itemId = itemId;
        this.owner.revisionId = ($('#map').data('revision') || 0);
        this.owner.forwardGeocoderUrl = $('#map').data('forward-geocoder-url');
        this.owner.reverseGeocoderUrl = $('#map').data('reverse-geocoder-url');
        this.owner.apiKey = $('#map').data('api-key');

        this.getConfig(configUrl);
    },

    // Get the JSON map config via AJAX from backend
    getConfig: function (configUrl) {
        $.getJSON(configUrl, function (data, status) {
            //console.log(status);
            // Store config data to map object
            osm_map.config = data;

            // Apply all config options and start creating the map
            osm_map.applyConfig();
        });
    },

    // Apply all config options and add vector layers accordingly
    applyConfig: function () {
        // Draw the map
        osm_map.display(this.config.map_lon || 0, this.config.map_lat || 0, this.config.map_zoom || 19);

        // Add vector layer with polygons
        if (this.config.api_polygons) {
            let url = this.config.api_polygons + '&item=' + this.owner.itemId;
            url += '&revision=' + this.owner.revisionId;
            this.getPolygonLayers(url);
        }

        // Add vector layer with points
        if (this.config.api_points) {
            let url = this.config.api_points + '&item=' + this.owner.itemId;
            url += '&revision=' + this.owner.revisionId;
            this.addVectorLayer(url,
                this.config.marker_icon,
                this.config.marker_color,
                this.config.marker_scale,
                this.config.marker_label
            );

            // Display error message if no points with valid lat/lon available
            console.log(osm_map.geoJsonLayer.getSource());
            if (this.geoJsonLayer.getSource().getFeatures().length == 'foo') {
                $('#mapError').css('display', 'block');
            }
        }

        // No lat/lon was given in map config, so we zoom to vector layer extent
        if (!this.config.map_lon || !this.config.map_lat) {
            //console.log('no lon/lat in config: zoom to extent');
            // Fires but does not give an extent:
            //this.geoJsonLayer.getSource().on('featuresloadend', function () {
            this.map.once('rendercomplete', function () {
                //console.log(osm_map.geoJsonLayer.getSource().getState());
                osm_map.moveMapToLayerSourceExtent(osm_map.geoJsonLayer, 50, osm_map.config.map.zoom);

                // Check non-empty for lat/lon form fields to respect old form input
                if ($('input.location_lat').val() || $('input.location_lon').val()) {
                    osm_map.updatePosition($('input.location_lon').val(), $('input.location_lat').val());
                    osm_map.moveMarker($('input.location_lon').val(), $('input.location_lat').val(),
                        osm_map.owner.itemId);
                }
                else {
                    // Use browser's geolocation API if available and enabled in config
                    if (Boolean(osm_map.config.map_geolocation) && navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            let lon = position.coords.longitude;
                            let lat = position.coords.latitude;
                            osm_map.updatePosition(lon, lat);
                            osm_map.moveMarker(lon, lat, osm_map.owner.itemId);
                        }, function (error) {
                            console.log("error using browser's geolocation API");
                        }, {
                            timeout:2500
                        });
                    }
                }
            });
        }

        // Add scale line
        if (this.config.scale_line) {
            this.addScaleLine();
        }

        // Add mouse position
        if (this.config.mouse_position) {
            this.addMousePosition(this.config.mouse_position);
        }

        // Add WMS layer
        if (this.config.wms_url) {
            // String with layer names, concatenated with commas
            var layers = '';
            if (this.config.wms_layers) {
                layers = this.config.wms_layers;
            }
            console.log(layers);
            // Array with 4 float elements representing the extent
            var extent = false;
            if (this.config.wms_extent) {
                extent = this.config.wms_extent;
            }
            console.log(extent);

            this.addWmsLayer(this.config.wms_url, layers, extent);
        }

        // TODO: move to new module
        // Click on map to update position of marker
        osm_map.map.on('singleclick', function (evt) {
            let coord = osm_map.transformCoordinate(evt.coordinate);
            // Update lat/lon form fields
            $('input.location_lon').val(coord[0]);
            $('input.location_lat').val(coord[1]);
            // Update position of marker
            osm_map.moveMarker($('input.location_lon').val(), $('input.location_lat').val(),
                osm_map.owner.itemId);
            
            // Check for missing location input to avoid useless geocoder query
            if (!$('input.location_city').length) {
                return;
            }

            // Fetch data from geocoder API
            $.ajax({
                url: osm_map.owner.reverseGeocoderUrl,
                type: 'get',
                dataType: 'json',
                data: {
                    format: 'json',
                    'accept-language': 'de',
                    lat: coord[1],
                    lon: coord[0],
                    key: osm_map.owner.apiKey,
                },
                success: function(data) {
                    //console.log(data);
                    // Update address form input fields with results from geocoder
                    $('input.location_country').val(data.address.country);
                    $('input.location_state').val(data.address.state);
                    $('input.location_county').val(data.address.county);
                    $('input.location_city').val(osm_map.getTownFromAddress(data.address));
                    $('input.location_street').val(data.address.road);
                    $('input.location_postcode').val(data.address.postcode);
                },
                error:function (xhr) {
                    //console.log(xhr);
                    // In case of wrong API key, the server responds with HTTP 403, but XHR fails
                    // because of missing CORS headers
                    if (xhr.status == 0) {
                        error_message = '@lang("common.geocoder_api_key_error")';
                    }
                    // Render the geocoder error message
                    $('#alertModalLabel').text('@lang("common.laravel_error")');
                    $('#alertModalContent').html('<div class="alert alert-danger">' + error_message + '</div>');
                    $('#alertModal').modal('show');
                },
            });
        });

        // Autocomplete search for address form input
        $('input.location_city').autocomplete( {
            disabled: true,
            minLength: 5,
            source: function(request, response) {
                // Fetch data from geocoder API
                $.ajax({
                    url: osm_map.owner.forwardGeocoderUrl,
                    type: 'get',
                    dataType: 'json',
                    data: {
                        format: 'json',
                        'accept-language': 'de',
                        addressdetails: '1',
                        q: request.term,
                        key: osm_map.owner.apiKey,
                    },
                    success: function(data) {
                        var transformed = $.map(data, function (element) {
                            //console.log(element);
                            return {
                                label: element.display_name + ' (' + element.class + ')',
                                value: element.osm_id,
                                address: element.address,
                                latitude: element.lat,
                                longitude: element.lon,
                                osm_id: element.osm_id,
                            };
                        });
                        //console.log(transformed);
                        response(transformed);
                    },
                    error:function (xhr) {
                        //console.log(xhr);
                        // In case of wrong API key, the server responds with HTTP 403, but XHR fails
                        // because of missing CORS headers
                        if (xhr.status == 0) {
                            error_message = '@lang("common.geocoder_api_key_error")';
                        }
                        // Render the geocoder error message
                        $('#alertModalLabel').text('@lang("common.laravel_error")');
                        $('#alertModalContent').html('<div class="alert alert-danger">' + error_message + '</div>');
                        $('#alertModal').modal('show');
                    },
                });
            },
            select: function (event, ui) {
                //console.log(ui.item);
                event.preventDefault();
                $('input.location_city').autocomplete('disable');
                
                // Update address form input fields with results from geocoder
                $('input.location_country').val(ui.item.address.country);
                $('input.location_state').val(ui.item.address.state);
                $('input.location_county').val(ui.item.address.county);
                $('input.location_city').val(osm_map.getTownFromAddress(ui.item.address));
                $('input.location_street').val(ui.item.address.road);
                $('input.location_postcode').val(ui.item.address.postcode);
                $('input.location_lat').val(ui.item.latitude);
                $('input.location_lon').val(ui.item.longitude);
                
                // Adjust map center to location of selected search result
                osm_map.updatePosition($('input.location_lon').val(), $('input.location_lat').val());
                osm_map.moveMarker($('input.location_lon').val(), $('input.location_lat').val(),
                    osm_map.owner.itemId);
                
                return false;
            }
        });
        
        // Send search request to geocoder API
        $('button.searchAddressBtn').click(function(xhr) {
            xhr.preventDefault();
            $('input.location_city').autocomplete('enable');
            $('input.location_city').autocomplete('search');
        });
    },

    // Extract name of city/town/village from geocoder address response
    getTownFromAddress: function (address) {
        if(address.city) {
            return address.city;
        }
        else {
            if(address.town) {
                return address.town;
            }
            else {
                if(address.village) {
                    return address.village;
                }
                else {
                    return "?";
                }
            }
        }
    },

    display: function (lon, lat, zoom) {
        
        var position = fromLonLat([lon, lat]);

        var view = new View({
            center: position,
            zoom: zoom,
            maxZoom: 19,
        });

        this.map = new Map({
            target: 'map',
            layers: [
                new TileLayer({
                    source: new OSM()
                }),
                this.vectorLayer,
            ],
            view: view,
        });
        
        var element = document.getElementById('popup');
        if (element) {
            this.popup = new Overlay({
                element: element,
                positioning: 'bottom-center',
                stopEvent: false,
                offset: [0, 15],
            });
            this.map.addOverlay(this.popup);
        }
    },
    
    updateSize: function () {
        this.map.updateSize();
    },

    // Add a control showing a scale line or bar
    addScaleLine: function () {
        const scaleLine = new ScaleLine({
            units: 'metric',
            bar: true,
            steps: 4,
            minWidth: 100,
        });
        this.map.addControl(scaleLine);
    },

    // Add a control showing the current position of mouse pointer
    addMousePosition: function (precision = 4) {
        const mousePositionControl = new MousePosition({
            coordinateFormat: createStringXY(precision),
            projection: 'EPSG:4326',
            // comment out the following two lines to have the mouse position
            // be placed outside the map.
            //className: 'custom-mouse-position',
            //target: document.getElementById('mouse-position'),
        });
        this.map.addControl(mousePositionControl);
    },
    
    addMarker: function (lon, lat, icon, color, scale=1.0, id, layer = this.vectorLayer) {
        if (typeof id === 'undefined') {
            id = 'defaultMarker';
        }
        var marker = new Feature({
            geometry: new Point(fromLonLat([lon, lat])),
        });
        
        marker.setId(id);
        marker.setStyle(
            new Style({
                image: new Icon({
                    color: color,
                    crossOrigin: 'anonymous',
                    src: icon,
                    scale: scale,
                }),
            })
        );
        layer.getSource().addFeature(marker);
    },
    
    updatePosition: function (lon, lat, zoom) {
        this.map.getView().setCenter(fromLonLat([lon, lat]));
    },

    // Move and zoom the map view to extent of a given layer's source
    moveMapToLayerSourceExtent: function (layer, padding = 50, maxZoom = 17) {
        var extent = layer.getSource().getExtent();
        //console.log(osm_map.transformExtent(extent));
        if (!olExtent.isEmpty(extent)) {
            osm_map.map.getView().fit(extent, {
                padding: [padding, padding, padding, padding],
                maxZoom: maxZoom,
            });
        }
    },
    
    // TODO: check if this is beeing used, otherwise remove
    moveMapToFeatureExtent: function (padding = 50, maxZoom = 17) {
        var extent = this.vectorLayer.getSource().getExtent();
        //console.log(osm_map.transformExtent(this.vectorLayer.getSource().getExtent(extent)));
        this.map.getView().fit(extent, {
            padding: [padding, padding, padding, padding],
            maxZoom: maxZoom,
        });
    },

    moveMarker: function (lon, lat, id = 'defaultMarker', layer = this.geoJsonLayer) {
        var coordinates = fromLonLat([lon, lat]);
        // Check for existing marker, otherwise create new one
        if (layer.getSource().getFeatureById(id)) {
            layer.getSource().getFeatureById(id).getGeometry().setCoordinates(coordinates);
        }
        else {
            //console.log('adding new marker because no existing one found for moving');
            this.addMarker(lon, lat,
                this.config.marker_icon, this.config.marker_color, this.config.marker_scale,
                '0', layer);
        }
    },
    
    removeMarker: function (id, layer = this.geoJsonLayer) {
        if (typeof id === 'undefined') {
            id = 'defaultMarker';
        }
        var feature = layer.getSource().getFeatureById(id);
        if (feature) {
            layer.getSource().removeFeature(feature);
        }
    },
    
    transformCoordinate: function (coordinate) {
        return transform(coordinate, 'EPSG:3857', 'EPSG:4326');
    },
    
    transformExtent: function (extent) {
        return transformExtent(extent, 'EPSG:3857','EPSG:4326');
    },

    addWmsLayer: function (url, layers, extent) {
        var tileLayer =   new TileLayer({
            extent: extent,
            source: new TileWMS({
                url: url,
                params: {'LAYERS': layers},
                // Do not fade tiles:
                transition: 0,
            }),
        });
        this.map.addLayer(tileLayer);
    },

    // Get all vector layers with polygon features from GeoJSON file
    getPolygonLayers: function (url) {
        //console.log('get polygon layers');
        $.getJSON(url, function (data, status) {
            var polygonLayers = data;
            //console.log(polygonLayers);

            polygonLayers.forEach(function (l, i) {
                osm_map.addPolygonLayer(l.polygon_file, l.polygon_color);
            });
        });
    },

    // Add a vector layer with polygon features from GeoJSON file
    addPolygonLayer: function (url, color) {
        const style = new Style({
            fill: new Fill({
                color: '#eeeeee',
            }),
            stroke: new Stroke({
                color: '#0000003f',
                width: 2,
            }),
        });

        var polygonLayer = new VectorLayer({
            source: new VectorSource({
                projection: 'EPSG:3857',
                url: url,
                format: new GeoJSON(),
            }),
            zIndex: 20,
            style: function (feature) {
                style.getFill().setColor(color || '#ffffff');
                return style;
            },
        });
        this.map.addLayer(polygonLayer);
    },

    // Add a vector layer with point features from GeoJSON file
    addVectorLayer: function (url, icon, color, scale=1.0, label=false) {
        var styleCache = {};
        
        this.geoJsonLayer = new VectorLayer({
            source: new VectorSource({
                projection : 'EPSG:3857',
                url: url,
                format: new GeoJSON(),
            }),
            zIndex: 40,
            style: function (feature) {
                var size = feature.length;
                var style = styleCache[size];
                if (!style) {
                    style = new Style({
                        image: new Icon({
                            color: color,
                            crossOrigin: 'anonymous',
                            src: icon,
                            scale: scale,
                        }),
                    });
                    if (label) {
                        var text = new Text({
                            text: feature.get('name'),
                            font: '12px Calibri,sans-serif',
                            offsetY : 20,
                            padding: [2, 2, 2, 2],
                            backgroundFill: new Fill({
                                color: '#fff',
                            }),
                            fill: new Fill({
                                color: '#000',
                            }),
                        })
                        style.setText(text);
                    }
                }
                return style;
            },
        });
        this.map.addLayer(this.geoJsonLayer);
    },
    
    // Add a vector layer with clustered point features from GeoJSON file
    addGeoJsonLayer: function (url) {
        var styleCache = {};
        
        this.geoJsonLayer = new VectorLayer({
            source: new ClusterSource({
                distance: 30,
                source: new VectorSource({
                    projection : 'EPSG:3857',
                    url: url,
                    format: new GeoJSON(),
                }),
            }),
            style: function (feature) {
                var size = feature.get('features').length;
                var style = styleCache[size];
                if (!style) {
                    style = new Style({
                        image: new CircleStyle({
                            radius: 12,
                            fill: new Fill({color: 'rgba(52, 144, 220, 0.7)'}),
                            stroke: new Stroke({
                                color: '#000',
                                width: 2,
                            }),
                        }),
                        text: new Text({
                            text: size.toString(),
                            fill: new Fill({color: '#000'}),
                        }),
                    }),
                    styleCache[size] = style;
                }
                return style;
            },
        });
        this.map.addLayer(this.geoJsonLayer);
    },
    
    isCluster: function (feature) {
        console.log(feature);
        if (!feature || !feature.get('features')) { 
            return false;
        }
        return feature.get('features').length > 1;
    },
    
    getExtendOfFeatures: function (features) {
        if (this.isCluster(features)) {
            // is a cluster, so loop through all the underlying features
            var clusteredFeatures = features.get('features');
            var extent = clusteredFeatures[0].getGeometry().getExtent().slice(0);
            for (var i = 0; i < clusteredFeatures.length; i++) {
                olExtent.extend(extent, clusteredFeatures[i].getGeometry().getExtent());
            }
        } else {
            // not a cluster
            var extent = features.getGeometry().getExtent().slice(0);
        }
        
        return transformExtent(extent, 'EPSG:3857','EPSG:4326');
    },
    
    getBoundsOfView: function () {
        const extent = this.map.getView().calculateExtent(this.map.getSize());
        return transformExtent(extent, 'EPSG:3857', 'EPSG:4326');
    },
    
    wrapLon: function (value) {
        const worlds = Math.floor((value + 180) / 360);
        return value - worlds * 360;
    },
}

export default osm_map;
