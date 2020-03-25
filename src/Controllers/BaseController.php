<?php
namespace ClockIt\Baserepo\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as LaravelBaseController;
use Illuminate\Support\Facades\Route;

class BaseController extends LaravelBaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * viewの自動判定
     *
     * @param array $params
     * @param string|null $view
     * @return mixed
     */
    protected function view(array $params = [], string $view = null)
    {
        $view = $view ?: Route::currentRouteName();
        return view($view, $params);
    }
}
