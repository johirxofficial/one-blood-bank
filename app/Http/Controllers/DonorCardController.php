<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf as PdfFacade;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DonorCardController extends Controller
{
    public function download(Request $request): Response
    {
        $donor = $request->user()->donor;

        abort_unless($donor?->isApproved(), 403, 'শুধুমাত্র ভেরিফাইড ডোনাররা কার্ড ডাউনলোড করতে পারবেন।');

        $donor->load(['user', 'division', 'district', 'upazila', 'union', 'contacts']);

        $qrCode = QrCode::format('svg')
            ->size(150)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($donor->user->profileUrl());

        $siteName = (string) Setting::get('site.name', 'One Blood Bank');

        $pdf = PdfFacade::loadView('pdf.donor-card', [
            'donor' => $donor,
            'qrCode' => $qrCode,
            'qrDataUri' => 'data:image/svg+xml;base64,'.base64_encode($qrCode),
            'photo' => $this->photoDataUri($donor->user->avatarFilePath()),
            'initials' => $donor->user->initials(),
            'siteName' => $siteName,
            'siteTagline' => (string) Setting::get('site.tagline', 'রক্ত দিন, জীবন বাঁচান'),
            'siteMark' => collect(explode(' ', $siteName))->filter()->take(3)->map(fn (string $part): string => mb_substr($part, 0, 1))->implode(''),
        ])->setPaper('a6', 'landscape');

        $this->registerFonts($pdf);

        return $pdf->download('blood-donor-card-'.$donor->user->username.'.pdf');
    }

    public function preview(Request $request)
    {
        $donor = $request->user()->donor;

        abort_unless($donor?->isApproved(), 403);

        $donor->load(['user', 'division', 'district', 'upazila', 'union', 'contacts']);

        $qrCode = QrCode::format('svg')
            ->size(150)
            ->margin(1)
            ->generate($donor->user->profileUrl());

        return view('donor.card-preview', compact('donor', 'qrCode'));
    }

    /**
     * Embed the profile photo so DomPDF never needs filesystem or remote access.
     * WebP is converted to JPEG because DomPDF cannot render it directly.
     */
    private function photoDataUri(?string $path): ?string
    {
        if (! $path || ! is_file($path)) {
            return null;
        }

        $info = @getimagesize($path);

        if ($info === false) {
            return null;
        }

        $mime = $info['mime'] ?? 'image/jpeg';

        if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $image = @imagecreatefromwebp($path);

            if ($image !== false) {
                ob_start();
                imagejpeg($image, null, 88);
                $contents = (string) ob_get_clean();
                imagedestroy($image);

                return 'data:image/jpeg;base64,'.base64_encode($contents);
            }
        }

        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)) {
            return null;
        }

        $contents = @file_get_contents($path);

        return $contents === false ? null : 'data:'.$mime.';base64,'.base64_encode($contents);
    }

    /**
     * DomPDF ships with Latin fonts only, so the Bengali typeface used on the
     * card has to be registered before the template is rendered.
     */
    private function registerFonts(PDF $pdf): void
    {
        $fontDir = storage_path('app/dompdf-fonts');
        $tempDir = storage_path('app/dompdf-tmp');

        foreach ([$fontDir, $tempDir] as $directory) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        $pdf->setOption([
            'defaultFont' => 'HindSiliguri',
            'chroot' => base_path(),
            'isRemoteEnabled' => false,
            'font_dir' => $fontDir,
            'font_cache' => $fontDir,
            'temp_dir' => $tempDir,
        ]);

        $metrics = $pdf->getDomPDF()->getFontMetrics();

        $metrics->registerFont(
            ['family' => 'HindSiliguri', 'style' => 'normal', 'weight' => 'normal'],
            resource_path('fonts/HindSiliguri-Bold.ttf'),
        );

        $metrics->registerFont(
            ['family' => 'HindSiliguri', 'style' => 'normal', 'weight' => 'bold'],
            resource_path('fonts/HindSiliguri-Bold.ttf'),
        );
    }
}
