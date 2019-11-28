<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\API\CreateBankAPIRequest;
use App\Http\Requests\API\UpdateBankAPIRequest;
use App\Models\Bank;
use App\Repositories\BankRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class BankController
 * @package App\Http\Controllers\Api
 */

class BankController extends AppBaseController
{
    /** @var  BankRepository */
    private $bankRepository;

    public function __construct(BankRepository $bankRepo)
    {
        $this->bankRepository = $bankRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/banks",
     *      summary="Get a listing of the Banks.",
     *      tags={"Bank"},
     *      description="Get all Banks",
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
     *                  @SWG\Items(ref="#/definitions/Bank")
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
        $banks = $this->bankRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($banks->toArray(), 'Banks retrieved successfully');
    }


    /**
     * @param int $id
     * @return JsonResponse
     *
     * @SWG\Get(
     *      path="/banks/{id}",
     *      summary="Display the specified Bank",
     *      tags={"Bank"},
     *      description="Get Bank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Bank",
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
     *                  ref="#/definitions/Bank"
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
        /** @var Bank $bank */
        $bank = $this->bankRepository->find($id);

        if ($bank === null) {
            return $this->sendError('Bank not found');
        }

        return $this->sendResponse($bank->toArray(), 'Bank retrieved successfully');
    }
}
