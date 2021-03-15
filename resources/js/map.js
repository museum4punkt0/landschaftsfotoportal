import {Map, Feature, View} from 'ol';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import {Icon, Style} from 'ol/style';
import Point from 'ol/geom/Point';
import OSM from 'ol/source/OSM';
import {fromLonLat} from 'ol/proj';

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
}

export default osm_map;
