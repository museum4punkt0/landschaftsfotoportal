<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Specimen",
 *     type="object",
 *     required={"id", "scientific_name", "modified"},
 * )
 */
class Specimen extends JsonResource
{
    /**
     * @OA\Property(
     *     description="Internal ID of this record",
     *     example=42,
     *     type="integer",
     *     format="int32",
     * )
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *     description="Barcode of this specimen",
     *     example="GLM-162215",
     *     format="string",
     * )
     * @var string
     */
    private $barcode;

    /**
     * @OA\Property(
     *     description="Full scientific name including authority",
     *     example="Crataegus praemonticola Holub",
     *     format="string",
     *     maxLength=255,
     * )
     * @var string
     */
    private $scientific_name;

    /**
     * @OA\Property(
     *     description="Name of the person who identified this item",
     *     format="string",
     * )
     * @var string
     */
    private $identified_by;

    /**
     * @OA\Property(
     *     description="Date when this item was identified",
     *     format="string",
     * )
     * @var string
     */
    private $identification_date;

    /**
     * @OA\Property(
     *     description="Remarks on (the history of) identification",
     *     format="string",
     * )
     * @var string
     */
    private $remarks_identification;

    /**
     * @OA\Property(
     *     description="Country and State where this item was found",
     *     format="string",
     * )
     * @var string
     */
    private $geographic_hierarchy;

    /**
     * @OA\Property(
     *     description="Description of the place where this item was found",
     *     format="string",
     * )
     * @var string
     */
    private $locality;

    /**
     * @OA\Property(
     *     description="Name of the ordnance survey map (de: Messtischblatt)",
     *     format="string",
     * )
     * @var string
     */
    private $map_tk25;

    /**
     * @OA\Property(
     *     description="Natural regions of Germany (de: Naturraum)",
     *     format="string",
     * )
     * @var string
     */
    private $landscape;

    /**
     * @OA\Property(
     *     description="Habitat",
     *     format="string",
     * )
     * @var string
     */
    private $habitat;

    /**
     * @OA\Property(
     *     description="Altitude above sea level, unit is meter (m)",
     *     type="number",
     *     format="float",
     * )
     * @var float
     */
    private $altitude;

    /**
     * @OA\Property(
     *     description="Latitude",
     *     type="number",
     *     format="float",
     * )
     * @var float
     */
    private $latitude;

    /**
     * @OA\Property(
     *     description="Longitude",
     *     type="number",
     *     format="float",
     * )
     * @var float
     */
    private $longitude;

    /**
     * @OA\Property(
     *     description="The coordinate reference system (CRS) as EPSG-Code",
     *     example="4326",
     *     format="string",
     * )
     * @var string
     */
    private $coordinate_reference_system;

    /**
     * @OA\Property(
     *     description="Uncertainty of coordinates, unit is meter (m)",
     *     example=250,
     *     type="integer",
     *     format="int32",
     * )
     * @var integer
     */
    private $coordinate_uncertainty;

    /**
     * @OA\Property(
     *     description="Name of the person who collected this item",
     *     format="string",
     * )
     * @var string
     */
    private $collector;

    /**
     * @OA\Property(
     *     description="Date when the item was collected",
     *     format="string",
     * )
     * @var string
     */
    private $collection_date;

    /**
     * @OA\Property(
     *     description="Collection number",
     *     format="string",
     * )
     * @var string
     */
    private $collection_number;

    /**
     * @OA\Property(
     *     description="Remarks on the collected item",
     *     format="string",
     * )
     * @var string
     */
    private $remarks_plant;

    /**
     * @OA\Property(
     *     description="Remarks on the specimen",
     *     format="string",
     * )
     * @var string
     */
    private $remarks_object;

    /**
     * @OA\Property(
     *     description="URL to the web page showing this specimen",
     *     format="string",
     * )
     * @var string
     */
    private $reference;

    /**
     * @OA\Property(
     *     description="A collection of media ressources",
     *     type="array",
     *         @OA\Items(
     *             ref="#/components/schemas/Image"
     *         ),
     * )
     * @var array
     */
    private $media;

    /**
     * @OA\Property(
     *     description="Timestamp (RFC3339) the external record was modified",
     *     format="date-time",
     *     type="string",
     *     example="2022-05-25 17:50:45",
     * )
     * @var string
     */
    private $modified;


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
