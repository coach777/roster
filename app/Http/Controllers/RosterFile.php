<?php

namespace App\Http\Controllers;

use App\Services\RosterService;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class RosterFile extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        Validator::validate($request->all(), [
            'file' => [
                'required',
                File::types(['html'])
                    ->min(1)
                    ->max(1024),
            ],
        ]);

        $file = $request->file('file'); //guarded

        $content = $file->getContent(); //Should be between 1 and 1024 kilobytes

        $rosterService = new RosterService();
        try {
            $activities = $rosterService->addActivitiesFromRoster($content);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 422);
        }

        return response()->json([
            "success" => true,
            "message" => "File successfully uploaded",
            "activitiesCount" => count($activities),
        ]);
    }

}
