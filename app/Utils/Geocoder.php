<?php

namespace App\Utils;

use App\Location;
use App\Column;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Geocoder
{
    /**
     * Do forward geocoding: find latitude and longitude for a given address.
     *
     * @param  \App\Location $location
     * @return array
     */
    public static function forward(Location $location)
    {
        // Check presence of certain geo fields in current line for a structured query
        if ($location->city) {
            $search = "city=". urlencode($location->city);
            if ($location->country) 
                $search .= "&country=". urlencode($location->country);
            if ($location->state) 
                $search .= "&state=". urlencode($location->state);
            if ($location->postcode) 
                $search .= "&postalcode=". urlencode($location->postcode);
            if ($location->street) 
                $search .= "&street=". urlencode($location->street);
        }
        else
            $search = "q=". urlencode($location->locality);
        
        // Send search request to geocoder
        $results = json_decode(Geocoder::request(config('geo.geocoder_url'), $search), true);
        
        // 2nd try with different search parameters as a simple query
        if (!count($results)) {
            $search = "q=". urlencode($location->city .",". $location->street);
            Log::debug('Geocoder 2nd try: '. $search);
            $results = json_decode(Geocoder::request(config('geo.geocoder_url'), $search), true);
        }
        
        return $results;
    }

    /**
     * Do reverse geocoding: find address for given latitude and longitude.
     *
     * @param  \App\Location $location
     * @return JSON
     */
    public static function reverse(Location $location)
    {
        // Check presence of certain geo fields in current line
        if ($location->lat && $location->lon) {
            $search = "lat=". $location->lat ."&lon=". urlencode($location->lon);
            
            // Send search request to geocoder
            $results = json_decode(Geocoder::request(config('geo.reverse_geocoder_url'), $search), true);
        }
        
        return $results;
    }

    /**
     * Create a request and send it to the external geocoder service.
     *
     * @param  integer $height_thumb
     * @return void
     */
    private static function request($url, $search, $deleteCache = false)
    {
        if ($deleteCache) {
            Cache::forget(md5($search));
        }
        
        $response = Cache::get(md5($search));
        
        // Don't send the same request more than once
        if ($response) {
            Log::debug('Geocoder: cache hit');
        }
        else {
            $data = array(
                'format' => 'json',
                'accept-language' => 'de',
                'addressdetails' => 1,
                'key' => config('geo.api_key'),
            );
            $query = http_build_query($data) .'&'. $search;
            Log::debug('Geocoder query string: '. $query);
            
            // Setting the user agent to allow reporting mis-use
            $opts = array(
                'http' => array(
                    'method' => "GET",
                    //'header' => "User-agent: museum4punkt0\r\n" .
                    //            "From: landschaftsfotos@senckenberg.de\r\n",
                )
            );
            $context = stream_context_create($opts);
            
            // Send HTTP request to geocoder API
            $response = file_get_contents($url . $query, false, $context);
            
            Cache::put(md5($search), $response);
        }
        
        return $response;
    }
}
