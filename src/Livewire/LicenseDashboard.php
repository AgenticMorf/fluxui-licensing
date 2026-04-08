<?php

namespace AgenticMorf\FluxUILicensing\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use LucaLongo\Licensing\Enums\LicenseStatus;

class LicenseDashboard extends Component
{
    public function render(): View
    {
        $licenseModel = config('licensing.models.license');

        $stats = [
            'total' => $licenseModel::query()->count(),
            'active' => $licenseModel::query()->where('status', LicenseStatus::Active)->count(),
            'pending' => $licenseModel::query()->where('status', LicenseStatus::Pending)->count(),
            'expiring_soon' => $licenseModel::query()
                ->where('status', LicenseStatus::Active)
                ->whereBetween('expires_at', [now(), now()->addDays(30)])
                ->count(),
            'expired' => $licenseModel::query()
                ->whereIn('status', [LicenseStatus::Expired, LicenseStatus::Cancelled, LicenseStatus::Suspended])
                ->count(),
        ];

        $recentLicenses = $licenseModel::query()
            ->with(['scope', 'template'])
            ->where('status', LicenseStatus::Active)
            ->where(function ($q) {
                $q->whereNull('activated_at')
                    ->orWhere('activated_at', '>=', now()->subDays(7));
            })
            ->orderByDesc('activated_at')
            ->limit(5)
            ->get();

        $expiringLicenses = $licenseModel::query()
            ->with(['scope'])
            ->where('status', LicenseStatus::Active)
            ->whereBetween('expires_at', [now(), now()->addDays(30)])
            ->orderBy('expires_at')
            ->limit(5)
            ->get();

        return view('fluxui-licensing::livewire.fluxui-licensing.license-dashboard', [
            'stats' => $stats,
            'recentLicenses' => $recentLicenses,
            'expiringLicenses' => $expiringLicenses,
        ]);
    }
}
