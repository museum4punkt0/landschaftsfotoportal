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
    
    display: function (lon, lat, zoom) {
        
        var position = fromLonLat([lon, lat]);

        var marker = new Feature({
            geometry: new Point(position)
        });
        
        marker.setStyle(
            new Style({
                image: new Icon({
                    color: '#ff0000',
                    crossOrigin: 'anonymous',
                    src: '../storage/images/dot.svg',
                    scale: 1.0,
                }),
            })
        );
        
        var vectorSource = new VectorSource({
            features: [marker],
        });
        var vectorLayer = new VectorLayer({
            source: vectorSource,
        });

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
                vectorLayer,
            ],
            view: view,
        });
    },
    
    updateSize: function () {
        this.map.updateSize();
    },
}

export default osm_map;
