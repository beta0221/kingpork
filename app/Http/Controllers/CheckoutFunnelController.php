<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckoutFunnelTracker;

class CheckoutFunnelController extends Controller
{
    /**
     * 前端追蹤 API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function track(Request $request)
    {
        return CheckoutFunnelTracker::apiTrack($request);
    }
}
