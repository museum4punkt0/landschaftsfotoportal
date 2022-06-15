<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Item",
 *     description="An item could be a preserved specimen or a chromosome count belonging to the taxon.",
 *     type="object",
 *     required={"id", "basisOfRecord", "reference"},
 * )
 */
class Item extends JsonResource
{
    /**
     * @OA\Property(
     *     description="Internal ID of this record",
     *     type="integer",
     *     format="int32",
     *     example=1,
     * )
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *     description="Type of record (item)",
     *     enum={"PRESERVED_SPECIMEN", "CHROMOSOME_COUNT"},
     * )
     * @var string
     */
    private $basisOfRecords;

    /**
     * @OA\Property(
     *     description="Timestamp (RFC3339) the external record was crawled by this API",
     *     format="date-time",
     *     type="string",
     *     example="2022-05-25 17:50:45",
     * )
     * @var string
     */
    private $lastCrawled;

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
     * @OA\Property(
     *     description="URL to external API to fetch the record of this item",
     *     format="string",
     *     example="https://bestikri.senckenberg.de/api/v1/specimen/390",
     * )
     * @var string
     */
    private $reference;

    /**
     * @OA\Property(
     *     description="URL to the web page showing this item",
     *     format="string",
     *     example="https://bestikri.senckenberg.de/item/390",
     * )
     * @var string
     */
    private $link;


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
