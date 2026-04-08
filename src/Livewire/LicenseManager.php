<?php

namespace AgenticMorf\FluxUILicensing\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use LucaLongo\Licensing\Enums\LicenseStatus;
use LucaLongo\Licensing\Models\License;
use LucaLongo\Licensing\Models\LicenseScope;

class LicenseManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $scopeFilter = '';

    // Create modal
    public bool $showCreateModal = false;
    public string $createScopeId = '';
    public string $createTemplateId = '';
    public string $createMaxUsages = '1';
    public string $createExpiresAt = '';
    public array $createMeta = [];

    // Key reveal modal
    public bool $showKeyModal = false;
    public ?string $revealedKey = null;
    public ?string $revealedLicenseUid = null;

    // Confirm action modal
    public bool $showConfirmModal = false;
    public ?string $confirmingLicenseId = null;
    public string $confirmAction = '';
    public string $confirmMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingScopeFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['createScopeId', 'createTemplateId', 'createMaxUsages', 'createExpiresAt', 'createMeta']);
        $this->createMaxUsages = '1';
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createLicense(): void
    {
        $this->authorizeAction();

        $scopeModel = config('licensing.models.license_scope', LicenseScope::class);
        $scopeTable = (new $scopeModel())->getTable();

        $this->validate([
            'createScopeId' => ['required', Rule::exists($scopeTable, 'id')],
            'createMaxUsages' => ['required', 'integer', 'min:1'],
            'createTemplateId' => ['nullable'],
            'createExpiresAt' => ['nullable', 'date'],
        ]);

        $licenseModel = config('licensing.models.license', License::class);

        $data = [
            'license_scope_id' => $this->createScopeId,
            'max_usages' => (int) $this->createMaxUsages,
            'status' => LicenseStatus::Pending,
        ];

        if ($this->createTemplateId !== '') {
            $data['template_id'] = $this->createTemplateId;
        }

        if ($this->createExpiresAt !== '') {
            $data['expires_at'] = $this->createExpiresAt;
        }

        $license = $licenseModel::createWithKey($data);

        $this->showCreateModal = false;

        if ($license->license_key !== null) {
            $this->revealedKey = $license->license_key;
            $this->revealedLicenseUid = $license->uid;
            $this->showKeyModal = true;
        }
    }

    public function closeKeyModal(): void
    {
        $this->revealedKey = null;
        $this->revealedLicenseUid = null;
        $this->showKeyModal = false;
    }

    public function showKey(string $licenseId): void
    {
        $this->authorizeAction();

        $licenseModel = config('licensing.models.license', License::class);
        $license = $licenseModel::findOrFail($licenseId);

        if (! $license->canRetrieveKey()) {
            return;
        }

        $key = $license->retrieveKey();

        if ($key === null) {
            return;
        }

        $this->revealedKey = $key;
        $this->revealedLicenseUid = $license->uid;
        $this->showKeyModal = true;
    }

    public function confirmRegenerateKey(string $licenseId): void
    {
        $this->confirmingLicenseId = $licenseId;
        $this->confirmAction = 'regenerateKey';
        $this->confirmMessage = __('Regenerating the key will invalidate the current key. Any client using the current key will need to update to the new one.');
        $this->showConfirmModal = true;
    }

    public function confirmActivate(string $licenseId): void
    {
        $this->confirmingLicenseId = $licenseId;
        $this->confirmAction = 'activate';
        $this->confirmMessage = __('This will activate the license and allow it to be used.');
        $this->showConfirmModal = true;
    }

    public function confirmSuspend(string $licenseId): void
    {
        $this->confirmingLicenseId = $licenseId;
        $this->confirmAction = 'suspend';
        $this->confirmMessage = __('This will suspend the license and prevent it from being used.');
        $this->showConfirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->confirmingLicenseId = null;
        $this->confirmAction = '';
        $this->confirmMessage = '';
        $this->showConfirmModal = false;
    }

    public function executeConfirmedAction(): void
    {
        $this->authorizeAction();

        $id = $this->confirmingLicenseId;
        abort_unless($id !== null, 404);

        $licenseModel = config('licensing.models.license', License::class);
        $license = $licenseModel::findOrFail($id);

        match ($this->confirmAction) {
            'activate' => $license->activate(),
            'suspend' => $license->suspend(),
            'regenerateKey' => $this->handleRegenerateKey($license),
            default => null,
        };

        $this->cancelConfirm();
    }

    protected function handleRegenerateKey(mixed $license): void
    {
        if (! $license->canRegenerateKey()) {
            return;
        }

        $newKey = $license->regenerateKey();
        $this->revealedKey = $newKey;
        $this->revealedLicenseUid = $license->uid;
        $this->showKeyModal = true;
    }

    /**
     * Check an optional gate guard defined in config('fluxui-licensing.gate').
     * If no gate is configured, the action proceeds without further checks (the
     * route middleware already enforces authentication).
     */
    protected function authorizeAction(): void
    {
        $gate = config('fluxui-licensing.gate');

        if ($gate !== null) {
            abort_unless(Gate::allows($gate), 403);
        }
    }

    protected function licenses(): LengthAwarePaginator
    {
        $licenseModel = config('licensing.models.license', License::class);

        $query = $licenseModel::query()->with(['scope', 'template'])->withCount('usages');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('uid', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->scopeFilter !== '') {
            $query->where('license_scope_id', $this->scopeFilter);
        }

        return $query->orderByDesc('created_at')
            ->paginate(config('fluxui-licensing.per_page', 15));
    }

    protected function scopes(): Collection
    {
        $scopeModel = config('licensing.models.license_scope', LicenseScope::class);

        return $scopeModel::orderBy('name')->get();
    }

    protected function templatesForScope(): Collection
    {
        if ($this->createScopeId === '') {
            return collect();
        }

        $scopeModel = config('licensing.models.license_scope', LicenseScope::class);
        $scope = $scopeModel::find($this->createScopeId);

        return $scope ? $scope->templates()->orderBy('name')->get() : collect();
    }

    public function render(): View
    {
        return view('fluxui-licensing::livewire.fluxui-licensing.license-manager', [
            'licenses' => $this->licenses(),
            'scopes' => $this->scopes(),
            'templatesForScope' => $this->templatesForScope(),
            'statuses' => LicenseStatus::cases(),
        ]);
    }
}
