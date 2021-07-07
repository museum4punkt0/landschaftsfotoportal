<?php

namespace App;

use App\Utils\Geocoder;
use Illuminate\Support\Facades\Log;

class Location
{
    public $country_code;
    public $country;
    public $state;
    public $county;
    public $postcode;
    public $city;
    public $suburb;
    public $street;
    public $locality;
    public $lat;
    public $lon;
    private $geocoder_results = ['forward' => null, 'reverse' => null];

    public function __construct()
    {
    }

    /**
     * Get some location info as comma separated string.
     *
     * @return string
     */
    public function toString() {
        return $this->country . ", " . $this->city . ", " . $this->street;
    }

    /**
     * Do geocoding for this location and return results.
     *
     * @param  string $direction
     * @return array
     */
    public function getGeocodingResults($direction) {
        if ($direction == 'forward') {
            return $this->geocoder_results['forward'] ?? $this->forwardGeocode();
        }
        if ($direction == 'reverse') {
            return $this->geocoder_results['reverse'] ?? $this->reverseGeocode();
        }
        return false;
    }

    /**
     * Do forward geocoding: find latitude and longitude for a given location.
     *
     * @param  integer $result_index
     * @return array
     */
    private function forwardGeocode($result_index = 0)
    {
        $response = Geocoder::forward($this);
        
        if ($response) {
            $this->lat = floatval($response[$result_index]['lat']);
            $this->lon = floatval($response[$result_index]['lon']);
            $this->geocoder_results['forward'] = $response;
        }
        else {
            Log::info(__('common.geocoder_no_result', ['location' => $this->toString()]));
        }
        
        return $response;
    }

    /**
     * Do reverse geocoding: find address for given location.
     *
     * @return JSON
     */
    private function reverseGeocode()
    {
        $response = Geocoder::reverse($this);
        
        if ($response) {
            $this->country_code = $response['address']['country_code'];
            $this->country = $response['address']['country'];
            $this->state = $response['address']['state'];
            $this->county = $response['address']['county'];
            $this->postcode = $response['address']['postcode'];
            $this->city = $this->getCity($response['address']);
            $this->suburb = $response['address']['suburb'];
            $this->street = $this->getStreet($response['address']);
            $this->geocoder_results['reverse'] = $response;
        }
        
        return $response;
    }

    /**
     * Get the city from different address fields.
     *
     * @param  array $address
     * @return string
     */
    private function getCity($address) {
        if(isset($address['city'])) {
            return $address['city'];
        }
        else {
            if(isset($address['town'])) {
                return $address['town'];
            }
            else {
                if(isset($address['village'])) {
                    return $address['village'];
                }
                else {
                    return "";
                }
            }
        }
    }

    /**
     * Get the street from different address fields.
     *
     * @param  array $address
     * @return string
     */
    private function getStreet($address) {
        if(isset($address['road'])) {
            return $address['road'];
        }
        else {
            if(isset($address['pedestrian'])) {
                return $address['pedestrian'];
            }
            else {
                return "";
            }
        }
    }
}
