import {Map, Feature, View, Overlay} from 'ol';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import ClusterSource from 'ol/source/Cluster';
import GeoJSON from 'ol/format/GeoJSON';
import {Icon, Style, Circle as CircleStyle, Fill, Stroke, Text} from 'ol/style';
import * as olExtent from 'ol/extent';
import Point from 'ol/geom/Point';
import OSM from 'ol/source/OSM';
import {fromLonLat, toLonLat, transform, transformExtent} from 'ol/proj';

var osm_map = {
    map: false,
    
    popup: false,
    
    vectorLayer: new VectorLayer({
        source: new VectorSource({
            features: false,
        })
    }),
    
    display: function (lon, lat, zoom) {
        
        var position = fromLonLat([lon, lat]);

        var view = new View({
            center: position,
            zoom: zoom,
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
    
    addMarker: function (lon, lat, icon, color) {
        var marker = new Feature({
            geometry: new Point(fromLonLat([lon, lat]))
        });
        
        marker.setStyle(
            new Style({
                image: new Icon({
                    color: color,
                    crossOrigin: 'anonymous',
                    src: icon,
                    scale: 1.0,
                }),
            })
        );
        this.vectorLayer.getSource().addFeature(marker);
    },
    
    updatePosition: function (lon, lat, zoom) {
        this.map.getView().setCenter(fromLonLat([lon, lat]));
    },
    
    moveMarker: function (lon, lat) {
        var coordinates = fromLonLat([lon, lat]);
        this.vectorLayer.getSource().getFeatures()[0].getGeometry().setCoordinates(coordinates);
    },
    
    transformCoordinate: function (coordinate) {
        return transform(coordinate, 'EPSG:3857', 'EPSG:4326');
    },
    
    addGeoJsonLayer: function (url) {
        var styleCache = {};
        
        var geoJsonLayer = new VectorLayer({
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
        this.map.addLayer(geoJsonLayer);
    },
    
    getExtendOfFeatures: function (features) {
        var extent = features[0].getGeometry().getExtent().slice(0);
        features.forEach(function (feature) {
            olExtent.extend(extent, feature.getGeometry().getExtent());
        });
        
        return transformExtent(extent, 'EPSG:3857','EPSG:4326');
    },
}

export default osm_map;
