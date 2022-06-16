<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="0.4.2",
 *      title=L5_SWAGGER_UI_TITLE,
 *      description="This is a private API for FlorawebPlus.",
 *      @OA\Contact(
 *          email="sebastian.klemm@senckenberg.de"
 *      ),
 *      @OA\License(
 *          name="AGPL-3",
 *          url="https://opensource.org/licenses/AGPL-3.0"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 * )
 *
 * @OA\Tag(
 *     name="specimen",
 *     description="All about availabe specimens."
 * )
 *
 * @OA\Tag(
 *     name="taxon",
 *     description="All about availabe taxa."
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
