@php use Filament\Support\Enums\MaxWidth; @endphp
<div>
    {{ $this->actionForm }}

    <x-filament::modal
        id="edit-post"
        :close-by-clicking-away="true"
        :slideOver="true"
        :footer-actions="$this->getModalFooterActions()"
        :width="MaxWidth::SevenExtraLarge"
    >
        <form id="editPostsForm">
            {{ $this->editPostsForm }}
        </form>
    </x-filament::modal>
</div>
