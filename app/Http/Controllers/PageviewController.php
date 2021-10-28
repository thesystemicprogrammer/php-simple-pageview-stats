<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use App\Models\Pageview;
use App\Services\RefererHash\RefererHashCreator;
use App\Services\TimeBucket\TimeBucketCalculatorService;

class PageviewController extends Controller
{

    public function getAll(): JsonResponse
    {
        return response()->json(Pageview::all());
    }

    public function getAllCount(): JsonResponse
    {
        return response()->json(DB::table('pageviews')->selectRaw('uri, count(*) AS pageviews')->groupBy('uri')->get()->keyBy('uri'));
    }

    public function create(Request $request, RefererHashCreator $referHashCreator, TimeBucketCalculatorService $calculatorService): JsonResponse
    {

        $this->validate($request, [
            'uri' => 'required'
        ]);

        $pageview = new Pageview();
        $pageview->uri = $request->uri;
        $pageview->timestamp = $calculatorService->calculateTimeBucketTimestamp();
        $pageview->refererHash = $referHashCreator->createRefererHash($request, $pageview->timestamp);

        try {
            $pageview->save();
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['message' => 'Page already viewed within defined period, not counted as new entry'], Response::HTTP_OK);
            } else {
                return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return response()->json(['message' => 'Pageview for ' . $pageview->uri . ' increased'], Response::HTTP_CREATED);
    }
}
