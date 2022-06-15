<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Image",
 *     type="object",
 *     required={"title", "thumbnail"},
 * )
 */
class Image extends JsonResource
{
    /**
     * @OA\Property(
     *     description="Image title",
     *     format="string",
     *     maxLength=255,
     * )
     * @var string
     */
    private $title;

    /**
     * @OA\Property(
     *     description="Author, licence or attribution remarks",
     *     format="string",
     *     maxLength=255,
     * )
     * @var string
     */
    private $copyright;

    /**
     * @OA\Property(
     *     description="URL to thumbnail image file",
     *     format="string",
     *     maxLength=1023,
     * )
     * @var string
     */
    private $thumbnail;

    /**
     * @OA\Property(
     *     description="URL to zoomify image viewer",
     *     format="string",
     *     maxLength=1023,
     * )
     * @var string
     */
    private $zoomify;


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
