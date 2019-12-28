<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseTrait;
use App\Http\Requests\API\CompletePasswordResetRequest;
use App\Http\Requests\API\ResetPasswordRequest;
use App\Mail\PasswordReset;
use App\Mail\PasswordResetSuccessful;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Swagger\Annotations as SWG;

class AuthController extends AccessTokenController
{
    use AppBaseTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     * @SWG\Post(
     *      path="/api/v1/auth/token",
     *      summary="Login",
     *      tags={"Authentication"},
     *      description="Login with username and password , return auth token",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Auth token request",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/AuthTokenRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Successful",
     *          @SWG\Schema(ref="#/definitions/AuthTokenResponse")
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Bad request",
     *          @SWG\Schema(ref="#/definitions/AuthTokenResponseFailed")
     *      )
     * )
     */
    public function auth(ServerRequestInterface $request): JsonResponse
    {
        try {
            // issue token
            $tokenResponse = $this->issueToken($request);
            //convert response to json string
            $content = $tokenResponse->getContent();
            //convert json to array
            $data = json_decode($content, true);
            if (!isset($data['error'])) {
                $token_data = collect($data);
                //get username (default is :email)
                $username = $request->getParsedBody()['username'];
                /** @var User $user */
                $user = User::query()->with(['group.tasks'])->where('email', $username)->firstOrFail();
                $token_data->put('user', $user->toArray());
                return Response::json($token_data);
            }

            return Response::json($data, 401);

        } catch (ModelNotFoundException $e) { // email notfound
            return $this->sendError('User not found', '404');
        } catch (Exception $e) {
            return $this->sendError('"Unknown error' . $e->getMessage(), '500');
        }

    }

    /**
     * @param Request $request
     * @return Response
     * @SWG\Post(
     *      path="/api/v1/auth/token?",
     *      summary="Refresh Token",
     *      tags={"Authentication"},
     *      description="Refresh previous token, return auth token",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Refresh token request",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/RefreshTokenRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Successful",
     *          @SWG\Schema(ref="#/definitions/AuthTokenResponse")
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Bad request",
     *          @SWG\Schema(ref="#/definitions/AuthTokenResponseFailed")
     *      )
     * )
     */
    public function refresh(Request $request): Response
    {

    }

    /**
     * @SWG\Get(
     *      path="/api/v1/auth/logout",
     *      summary="Logout",
     *      tags={"Authentication"},
     *      description="Log out from device",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        if (Auth::check()) {
            try {
                die(print_r(Auth::user()->token(), true));
                /** @var User $user */
                Auth::user()->token()->revoke();

                return $this->sendResponse(null, 'Successfully logged out');
            } catch (Exception $exception) {
            }
        }
        return $this->sendError(null, 'Logged failed');
    }


    /**
     * @SWG\Get(
     *      path="/api/v1/auth/logout-all",
     *      summary="Logout from all devices",
     *      tags={"Authentication"},
     *      description="Log out from all device",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          description="bearer token",
     *          in="header",
     *          required=true
     *     ),
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     *     )
     * )
     */
    public function logoutFromAll(): JsonResponse
    {
        if (Auth::check()) {
            try {
                /** @var User $user */
                $user = Auth::user();
                $user->authAccessToken()->delete();
                return $this->sendResponse(null, 'Successfully logged out all devices');
            } catch (Exception $exception) {
                // return $this->sendError('Logged failed'.$exception->getMessage(), 403);
            }
        }
        return $this->sendError('Logged failed', 403);
    }

    /**
     * @SWG\Post(
     *      path="/api/v1/auth/reset-password",
     *      summary="Reset password",
     *      tags={"Authentication"},
     *      description="Sent password reset email",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="",
     *          required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="string",
     *                  property="email"
     *              ),
     *             @SWG\Property(
     *                type="string",
     *                property="return_url"
     *            )
     *         )
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     *     )
     * )
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $code = null;
        $email = $request->input('email');
        $return_url = $request->input('return_url', '');
        try {
            /** @var User $user */
            $user = User::query()->where('email', $email)->firstOrFail();
            $code = random_int(100000, 999999);
            Cache::put('reset-password-user-' . $code, $user->id, config('app.reset-password-token-expiry', 3600));


            // send reset email
            Mail::to($user->email)
                ->send(new PasswordReset($user, $code, $return_url));
            $response_data = ['success' => true, 'message' => 'Password reset email sent'];
            if (config('app.env') !== 'production') {
                $response_data['reset_code**tmp'] = $code;
                return Response::json($response_data);
            }
        } catch (Exception $exception) {
            return Response::json(ResponseUtil::makeError('Could not reset password. '.$exception->getMessage()), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/api/v1/auth/complete-password-reset",
     *      summary="Complete password reset",
     *      tags={"Authentication"},
     *      description="",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="",
     *          required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="string",
     *                  property="password"
     *              ),
     *             @SWG\Property(
     *                type="string",
     *                property="reset_code"
     *            )
     *         )
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     *     )
     * )
     * @param CompletePasswordResetRequest $request
     * @return JsonResponse
     */
    public function completePasswordReset(CompletePasswordResetRequest $request): JsonResponse
    {
        $reset_code = $request->input('reset_code');
        $password = $request->input('password');
        $user_id = Cache::pull('reset-password-user-' . $reset_code);
        if (!$user_id) {
            return Response::json(ResponseUtil::makeError('Reset code is not valid'), 400);
        }

        try {
            /** @var User $user */
            $user = User::query()->find($user_id);
            //send password successful
            Mail::to($user->email)
                ->send(new PasswordResetSuccessful($user ));
            $user->update(['password' => $password, 'status' => $user->status !==2 ? $user->status: 1]);
        } catch (Exception $exception) {
            return Response::json(ResponseUtil::makeError('Could not reset password'), 400);
        }
        return Response::json(['success' => true, 'message' => 'Password reset successful']);
    }
}
