<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\API\CreateLgaAPIRequest;
use App\Http\Requests\API\UpdateLgaAPIRequest;
use App\Models\Lga;
use App\Repositories\LgaRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class LgaController
 * @package App\Http\Controllers\Api
 */

class LgaController extends AppBaseController
{
    /** @var  LgaRepository */
    private $lgaRepository;

    public function __construct(LgaRepository $lgaRepo)
    {
        $this->lgaRepository = $lgaRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/api/v1/lgas",
     *      summary="Get a listing of the Lgas.",
     *      tags={"Lga"},
     *      description="Get all Lgas",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Lga")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $lgas = $this->lgaRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($lgas->toArray(), 'Lgas retrieved successfully');
    }


    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/api/v1/lgas/{id}",
     *      summary="Display the specified Lga",
     *      tags={"Lga"},
     *      description="Get Lga",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Lga",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Lga"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id): JsonResponse
    {
        /** @var Lga $lga */
        $lga = $this->lgaRepository->find($id);

        if ($lga === null) {
            return $this->sendError('Lga not found');
        }

        return $this->sendResponse($lga->toArray(), 'Lga retrieved successfully');
    }


}
