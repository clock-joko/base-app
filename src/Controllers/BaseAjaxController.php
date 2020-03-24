<?php
namespace ClockIt\Baserepo\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class BaseAjaxController extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * BaseAjaxController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }
    }

    /**
     * レスポンスフォーマット
     *
     * @param $status
     * @param $result
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($status, $result, $request)
    {
        return response()->json([
            'status' => $status,
            'result' => $result,
            'request' => $request
        ], 200);
    }
}
