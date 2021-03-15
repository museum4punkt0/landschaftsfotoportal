import {Map, Feature, View} from 'ol';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import {Icon, Style} from 'ol/style';
import Point from 'ol/geom/Point';
import OSM from 'ol/source/OSM';
import {fromLonLat, transform} from 'ol/proj';

var osm_map = {
    map: false,
    
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
    },
    
    updateSize: function () {
        this.map.updateSize();
    },
    
    addMarker: function (lon, lat, icon) {
        var marker = new Feature({
            geometry: new Point(fromLonLat([lon, lat]))
        });
        
        marker.setStyle(
            new Style({
                image: new Icon({
                    color: '#3490dc',
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
}

export default osm_map;
