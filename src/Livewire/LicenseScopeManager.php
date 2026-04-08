<?php

namespace AgenticMorf\FluxUILicensing\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class LicenseScopeManager extends Component
{
    public bool $showCreateModal = false;
    public string $createName = '';
    public string $createDescription = '';

    public bool $showEditModal = false;
    public ?string $editingId = null;
    public string $editName = '';
    public string $editDescription = '';

    public bool $showDeleteModal = false;
    public ?string $deletingId = null;

    public function openCreateModal(): void
    {
        $this->reset(['createName', 'createDescription']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createScope(): void
    {
        $this->validate([
            'createName' => ['required', 'string', 'max:255'],
        ]);

        $scopeModel = config('licensing.models.license_scope', \LucaLongo\Licensing\Models\LicenseScope::class);

        $scopeModel::create([
            'name' => $this->createName,
            'description' => $this->createDescription ?: null,
        ]);

        $this->showCreateModal = false;
    }

    public function openEditModal(string $scopeId): void
    {
        $scopeModel = config('licensing.models.license_scope', \LucaLongo\Licensing\Models\LicenseScope::class);
        $scope = $scopeModel::findOrFail($scopeId);

        $this->editingId = $scopeId;
        $this->editName = $scope->name;
        $this->editDescription = $scope->description ?? '';
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->editingId = null;
        $this->showEditModal = false;
    }

    public function updateScope(): void
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
        ]);

        $scopeModel = config('licensing.models.license_scope', \LucaLongo\Licensing\Models\LicenseScope::class);
        $scope = $scopeModel::findOrFail($this->editingId);

        $scope->update([
            'name' => $this->editName,
            'description' => $this->editDescription ?: null,
        ]);

        $this->showEditModal = false;
        $this->editingId = null;
    }

    public function confirmDelete(string $scopeId): void
    {
        $this->deletingId = $scopeId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
        $this->showDeleteModal = false;
    }

    public function deleteScope(): void
    {
        abort_unless($this->deletingId !== null, 404);

        $scopeModel = config('licensing.models.license_scope', \LucaLongo\Licensing\Models\LicenseScope::class);
        $scope = $scopeModel::findOrFail($this->deletingId);
        $scope->delete();

        $this->deletingId = null;
        $this->showDeleteModal = false;
    }

    protected function scopes(): Collection
    {
        $scopeModel = config('licensing.models.license_scope', \LucaLongo\Licensing\Models\LicenseScope::class);

        return $scopeModel::withCount('licenses')->orderBy('name')->get();
    }

    public function render(): View
    {
        return view('fluxui-licensing::livewire.fluxui-licensing.license-scope-manager', [
            'scopes' => $this->scopes(),
        ]);
    }
}
