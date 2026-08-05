<?php

declare(strict_types=1);

namespace Tests\Feature\DesignSystem;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class BladeComponentRenderTest extends TestCase
{
    public function test_button_variants_render(): void
    {
        $response = $this->blade(<<<'BLADE'
            <x-ui.button>Primary</x-ui.button>
            <x-ui.button variant="secondary">Secondary</x-ui.button>
            <x-ui.button variant="outline" disabled="true">Disabled</x-ui.button>
            <x-ui.link-button href="/">Link</x-ui.link-button>
            <x-ui.icon-button label="Icon label">I</x-ui.icon-button>
        BLADE);

        $response->assertSee('Primary');
        $response->assertSee('Disabled');
        $response->assertSee('aria-disabled="true"', false);
        $response->assertSee('aria-label="Icon label"', false);
    }

    public function test_form_components_render_accessible_markup(): void
    {
        $response = $this->blade(<<<'BLADE'
            <x-form.field label="Nama" for="name" error="Nama wajib" help="Isi nama lengkap.">
                <x-form.input id="name" name="name" />
            </x-form.field>
            <x-form.password-input id="password" name="password" />
            <x-form.textarea name="note">Catatan</x-form.textarea>
            <x-form.select name="choice" placeholder="Pilih"><option>A</option></x-form.select>
            <x-form.checkbox name="check" label="Setuju" />
            <x-form.switch name="switch" label="Aktif" />
        BLADE);

        $response->assertSee('Nama wajib');
        $response->assertSee('aria-invalid="true"', false);
        $response->assertSee('id="name-help"', false);
        $response->assertSee('id="name-error"', false);
        $response->assertSee('role="alert"', false);
        $response->assertSee('aria-describedby="name-help name-error"', false);
        $response->assertSee('x-bind:aria-label', false);
        $response->assertSee('aria-pressed', false);
        $response->assertSee('Setuju');
    }

    public function test_choice_components_forward_livewire_attributes_to_inputs(): void
    {
        $response = $this->blade(<<<'BLADE'
            <x-form.checkbox name="check" label="Setuju" wire:model.live="form.check" data-testid="check" required />
            <x-form.radio name="choice" value="a" label="Pilihan" wire:change="choose" />
            <x-form.switch name="active" label="Aktif" wire:model="form.active" disabled />
        BLADE);

        $html = (string) $response;

        $this->assertMatchesRegularExpression('/<input[^>]+name="check"[^>]+wire:model\.live="form\.check"[^>]+data-testid="check"[^>]+required[^>]*>/s', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+type="radio"[^>]+wire:change="choose"[^>]*>/s', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+name="active"[^>]+role="switch"[^>]+wire:model="form\.active"[^>]+disabled[^>]*>/s', $html);
        $this->assertDoesNotMatchRegularExpression('/<label[^>]+wire:model/s', $html);
        $this->assertDoesNotMatchRegularExpression('/<label[^>]+wire:change/s', $html);
    }

    public function test_feedback_and_data_components_render(): void
    {
        $paginator = new LengthAwarePaginator(
            items: collect([['name' => 'A']]),
            total: 30,
            perPage: 10,
            currentPage: 2,
            options: ['path' => '/admin/example'],
        );

        $response = $this->blade(<<<'BLADE'
            <x-ui.alert variant="success" title="Berhasil">Pesan</x-ui.alert>
            <x-ui.alert variant="danger" title="Gagal">Pesan</x-ui.alert>
            <x-ui.badge variant="warning">Menunggu</x-ui.badge>
            <x-ui.card title="Card">Isi</x-ui.card>
            <x-ui.empty-state title="Kosong" />
            <x-ui.error-state title="Kendala" />
            <x-data.table caption="Contoh">
                <x-data.table-head><tr><x-data.table-cell heading="true">Kolom</x-data.table-cell></tr></x-data.table-head>
                <x-data.table-body><x-data.table-empty colspan="1" /></x-data.table-body>
            </x-data.table>
            <x-data.pagination :paginator="$paginator" />
        BLADE, ['paginator' => $paginator]);

        $response->assertSee('Berhasil');
        $response->assertSee('Menunggu');
        $response->assertSee('Card');
        $response->assertSee('Kosong');
        $response->assertSee('role="alert"', false);
        $response->assertSee('caption', false);
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('Navigasi halaman');
    }

    public function test_overlay_and_navigation_components_render(): void
    {
        $response = $this->blade(<<<'BLADE'
            <x-ui.modal name="demo" title="Modal">
                <x-slot:trigger><button type="button">Buka</button></x-slot:trigger>
                Isi
            </x-ui.modal>
            <x-ui.confirmation-dialog name="confirm">
                <x-slot:trigger><button type="button">Konfirmasi</button></x-slot:trigger>
                <x-slot:confirm><button type="button" wire:click="confirm">Ya</button></x-slot:confirm>
            </x-ui.confirmation-dialog>
            <x-ui.tabs :tabs="['one' => 'Satu', 'two' => 'Dua']" :panels="['one' => 'Panel satu', 'two' => 'Panel dua']" />
            <x-ui.dropdown label="Menu"><span>Item</span></x-ui.dropdown>
            <x-navigation.page-header title="Judul" description="Deskripsi" />
            <x-navigation.sidebar />
            <x-navigation.mobile-nav />
        BLADE);

        $response->assertSee('role="dialog"', false);
        $response->assertSee('aria-modal="true"', false);
        $response->assertSee('trapTab', false);
        $response->assertSee('wire:click="confirm"', false);
        $response->assertSee('role="tablist"', false);
        $response->assertSee('role="tab"', false);
        $response->assertSee('role="tabpanel"', false);
        $response->assertSee('aria-controls', false);
        $response->assertSee('x-on:keydown.arrow-right.prevent', false);
        $response->assertSee('aria-expanded', false);
        $response->assertSee('Judul');
        $response->assertSee('Navigasi admin');
        $response->assertSee('Navigasi bawah');
    }
}
