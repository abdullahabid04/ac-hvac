<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateReportRequest;
use App\Models\AcUnit;
use App\Models\Building;
use App\Models\Floor;
use App\Models\GeneratedReport;
use App\Models\Site;
use App\Models\Space;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');

        $reports = GeneratedReport::where('organization_id', $organization->id)
            ->with('requestedBy')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('reports', [
            'organization' => $organization,
            'reports' => $reports,
            'reportTypes' => config('hvac.report_types'),
            'sites' => Site::where('organization_id', $organization->id)->orderBy('name')->get(),
            'buildings' => Building::where('organization_id', $organization->id)->orderBy('name')->get(),
            'floors' => Floor::where('organization_id', $organization->id)->orderBy('name')->get(),
            'spaces' => Space::where('organization_id', $organization->id)->with('floor.building')->orderBy('name')->get(),
            'acs' => AcUnit::where('organization_id', $organization->id)->orderBy('name')->get(),
        ]);
    }

    public function store(GenerateReportRequest $request)
    {
        $organization = $request->attributes->get('organization');
        $data = $request->validated();

        $report = $this->reports->request(
            $organization,
            $request->user(),
            $data['report_type'],
            array_filter([
                'from' => $data['from'],
                'to' => $data['to'],
                'site_id' => $data['site_id'] ?? null,
                'building_id' => $data['building_id'] ?? null,
                'floor_id' => $data['floor_id'] ?? null,
                'space_id' => $data['space_id'] ?? null,
                'ac_id' => $data['ac_id'] ?? null,
            ], fn ($value) => $value !== null && $value !== ''),
            $data['format'],
        );

        if ($report->status !== 'completed') {
            return back()->with('error', 'The report could not be generated. Please try again.');
        }

        return redirect()->route('reports.index')->with('success', 'Report generated. Use the download action to retrieve it.');
    }

    public function download(Request $request, GeneratedReport $report)
    {
        $organization = $request->attributes->get('organization');

        if ($report->organization_id !== $organization->id) {
            abort(404);
        }

        if ($report->status !== 'completed' || ! $report->file_path || ! Storage::disk('local')->exists($report->file_path)) {
            return back()->with('error', 'The report file is not available.');
        }

        $extension = pathinfo($report->file_path, PATHINFO_EXTENSION);
        $filename = str($report->title)->slug().'-'.now()->format('Ymd-His').'.'.$extension;

        return Storage::disk('local')->download($report->file_path, $filename, [
            'Content-Type' => $extension === 'csv' ? 'text/csv' : 'application/pdf',
        ]);
    }

    public function destroy(Request $request, GeneratedReport $report)
    {
        $organization = $request->attributes->get('organization');

        if ($report->organization_id !== $organization->id) {
            abort(404);
        }

        if ($report->file_path && Storage::disk('local')->exists($report->file_path)) {
            Storage::disk('local')->delete($report->file_path);
        }

        $report->delete();

        return back()->with('success', 'Report removed.');
    }
}
