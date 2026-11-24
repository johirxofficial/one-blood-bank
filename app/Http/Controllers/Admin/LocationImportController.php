<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LocationImportController extends Controller
{
    public function index(): View
    {
        return view('admin.locations.index', [
            'countries' => Division::query()
                ->select('country_code')
                ->selectRaw('count(*) as divisions')
                ->groupBy('country_code')
                ->orderBy('country_code')
                ->get(),
            'counts' => [
                'division' => Division::query()->count(),
                'district' => District::query()->count(),
                'upazila' => Upazila::query()->count(),
                'union' => Union::query()->count(),
            ],
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'country' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
        ]);

        $country = strtoupper($data['country']);
        $exitCode = Artisan::call('locations:import', [
            'file' => $request->file('file')->getRealPath(),
            '--country' => $country,
        ]);

        $output = Artisan::output();

        if ($exitCode !== 0) {
            return back()
                ->withInput()
                ->with('error', 'ইমপোর্ট ব্যর্থ হয়েছে।')
                ->with('import_output', $output);
        }

        return back()
            ->with('success', "{$country} এর লোকেশন সফলভাবে ইমপোর্ট হয়েছে।")
            ->with('import_output', $output);
    }

    public function sample(): StreamedResponse
    {
        $rows = [
            ['level', 'name', 'name_bn', 'parent', 'code', 'latitude', 'longitude'],
            ['division', 'Example Division', 'উদাহরণ বিভাগ', '', 'EX-DIV', '', ''],
            ['district', 'Example District', 'উদাহরণ জেলা', 'Example Division', 'EX-DIS', '', ''],
            ['upazila', 'Example Upazila', 'উদাহরণ উপজেলা', 'Example District', 'EX-UPA', '', ''],
            ['union', 'Example Union', 'উদাহরণ ইউনিয়ন', 'Example Upazila', 'EX-UNI', '', ''],
        ];

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'locations-example.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
