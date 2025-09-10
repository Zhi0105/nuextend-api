<?php

namespace App\Http\Controllers;

use App\Models\ProgressReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProgressReportController extends Controller
{


    public function index($id) {
        try {
            $reports = ProgressReport::where('activity_id', $id)->get();

            return response()->json([
                'status' => 200,
                'data' => $reports
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }

    public function store(Request $request) {
    // 1) Validate nested input + files
    $validated = $request->validate([
        'reports' => ['required','array','min:1'],

        // adjust to required/nullable as your business rules need
        'reports.*.event_id'    => ['required'],
        'reports.*.activity_id' => ['required'],
        'reports.*.name'        => ['nullable'],
        'reports.*.date'        => ['nullable'],
        'reports.*.budget'      => ['nullable'],

        // file is optional per item; if provided must be PDF ≤10MB
        'reports.*.file'        => ['nullable','file','mimes:pdf','max:10240'],
    ]);

    try
    {
        $created = DB::transaction(function () use ($request, $validated) {
                $rows = [];

                // iterate using index to fetch the UploadedFile correctly
                foreach ($validated['reports'] as $i => $report) {
                    $fileUrl = null;

                    // IMPORTANT: get the file via the request path "reports.$i.file"
                    if ($file = $request->file("reports.$i.file")) {
                        // store on "public" disk -> storage/app/public/pdf/reports/...
                        // Storage::url() will return "/storage/..." public path
                        $path   = $file->store('pdf/reports', 'public');
                        $fileUrl = Storage::url($path);
                    }

                    $rows[] = [
                        'event_id'    => $report['event_id']    ?? null,
                        'activity_id' => $report['activity_id'] ?? null,
                        'name'        => $report['name'],
                        'file'        => $fileUrl, // null if no file provided
                        'date'        => $report['date']        ?? null,
                        'budget'      => $report['budget']      ?? null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }

                // Use insert for performance (skip model events). If you rely on events/observers, loop with ::create instead.
                ProgressReport::insert($rows);

                return $rows;
            });

            return response()->json([
                'status'  => 201,
                'message' => 'New progress reports created',
                'data'    => $created, // optional: remove if you don’t want to echo details
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong while saving reports.',
            ], 500);
        }
    }
    public function delete(Request $request) {
    $report = ProgressReport::findOrFail($request->report_id);

        // If $report->file is a URL like https://site.com/storage/reports/file.pdf,
        // convert it to a disk path: "reports/file.pdf"
        $path = $report->file;

        // 1) If it's a full URL, strip domain + leading "/storage/"
        //    (leaves "reports/filename.pdf")
        $urlPath = parse_url($path, PHP_URL_PATH);              // e.g. "/storage/reports/a.pdf"
        $urlPath = ltrim($urlPath, '/');                        // "storage/reports/a.pdf"
        $path    = preg_replace('#^storage/#', '', $urlPath);   // "reports/a.pdf"

        // 2) If you actually stored "public/reports/..." in DB, normalize it too.
        $path = preg_replace('#^public/#', '', $path);          // "reports/a.pdf"

        // Now delete on the "public" disk
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Optional: remove the DB record
        $report->delete();

        return response()->json(['message' => 'Removed successfully'], 200);
    }

    public function approve(Request $request) {

        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
            "commex_remarks" => 'sometimes',
            "asd_remarks" => 'sometimes'
        ]);

            try {
            $report = ProgressReport::find($request->id);

            if (!$report) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Report not found',
                ], 404);
            }

            $userId = auth()->id(); // current logged-in user

            $roleUpdateMap = [
                1  => ['is_commex' => true, 'commex_remarks' => $request->input('commex_remarks'), 'commex_approve_date' => now()],
                10 => ['is_asd' => true, 'asd_remarks' => $request->input('asd_remarks'),  'asd_approve_date' => now()],
            ];

            if (isset($roleUpdateMap[$request->role_id])) {
                $report->update($roleUpdateMap[$request->role_id]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Approved Successful',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }

    }
    public function reject(Request $request) {
        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
            "commex_remarks" => 'sometimes',
            "asd_remarks" => 'sometimes'
        ]);

        try {
            $report = ProgressReport::find($request->id);

            if (!$report) {
                return response()->json([
                    'status' => 404,
                    'message' => 'report not found',
                ], 404);
            }

            $roleUpdateMap = [
                1  => ['is_commex' => false, 'commex_remarks' => $request->input('commex_remarks')],
                10 => ['is_asd' => false, 'asd_remarks' => $request->input('asd_remarks')],
            ];

            $updateData = $roleUpdateMap[$request->role_id] ?? null;

            if ($updateData) {
                $report->update($updateData);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Report Rejected',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
