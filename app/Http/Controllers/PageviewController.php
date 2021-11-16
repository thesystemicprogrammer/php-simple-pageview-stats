<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use App\Models\Pageview;
use App\Models\RefererPageview;
use App\Services\RefererHash\RefererHashCreator;
use App\Services\TimeBucket\TimeBucketCalculatorService;
use Illuminate\Support\Facades\Log;

class PageviewController extends Controller {

    public function getPageviews(): JsonResponse {

        $result = Pageview::groupBy('uri')
            ->selectRaw('uri, sum(views) AS views')
            ->get();

        return response()->json($result->keyBy('uri'));
    }

    public function getPageviewsGroupedByPeriods(Request $request): JsonResponse {
        $fromTimestamp = $request->get('from') ?? 0;
        $toTimestamp = $request->get('to') ?? PHP_INT_MAX;

        if ($fromTimestamp > $toTimestamp) {
            return response()->json(['message' => 'FROM Timestamp must not be bigger than the TO timestamp'], Response::HTTP_BAD_REQUEST); 
        }

        $result = DB::table('pageviews')
            ->selectRaw('timestamp, uri, views')
            ->whereBetween('timestamp', [$fromTimestamp, $toTimestamp])
            ->get();

        return response()->json($result);
    }

    public function createPageview(Request $request, RefererHashCreator $referHashCreator, TimeBucketCalculatorService $calculatorService): JsonResponse {

        $this->validate($request, [
            'uri' => 'required'
        ]);

        $pageview = new RefererPageview();
        $pageview->uri = $request->uri;
        $pageview->timestamp = $calculatorService->calculateTimeBucketTimestamp();
        $pageview->refererHash = $referHashCreator->createRefererHash($request, $pageview->timestamp);

        try {
            $pageview->save();
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json(['message' => 'Page already viewed within defined period, not counted as new entry'], Response::HTTP_OK);
            } else {
                return response()->json(['errorCode' => $e->getCode(), 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return response()->json(['message' => 'Pageview for ' . $pageview->uri . ' increased'], Response::HTTP_CREATED);
    }
}
