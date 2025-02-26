<div>
    {{ $this->table }}

    @if(!is_null($post) && $post->pinnedComments->count() > 0 && $this->renderedCount === 0)
        @script
        <script>
            $wire.mountTableBulkAction('pinComments', @json($post->pinnedComments->pluck('comment_id')))
        </script>
        @endscript
    @endif

    <script>
        document.addEventListener('trigger-select-records-{{ $this->id() }}', function(event) {
            event.detail[0].records.forEach(record => {
                const checkbox = document.querySelector('tr[wire\\:key="' + event.detail[0].livewireId + '.table.records.' + record + '"] input[type="checkbox"]')
                checkbox.click()
            });
        })
    </script>
    @script
    {{-- Call the bulk action each time a checkbox is clicked so we get the correct behaviour
     but not require the user click twice to select a medical --}}
    <script>
        const testSelectedRecords_{{ $this->id() }} = [];
        document.querySelectorAll('tr[wire\\:key^="{{ $this->id() }}.table.records."] input[type="checkbox"]')
            .forEach((checkbox) => {
                checkbox.addEventListener('click', (event) => {
                    const targetCheckbox = event.target;

                    if (targetCheckbox.checked) {
                        testSelectedRecords_{{ $this->id() }}.push(targetCheckbox.value);
                    } else {
                        const index = testSelectedRecords_{{ $this->id() }}.indexOf(targetCheckbox.value);
                        if (index !== -1) {
                            testSelectedRecords_{{ $this->id() }}.splice(index, 1);
                        }
                    }
                    $wire.mountTableBulkAction('pinComments', testSelectedRecords_{{ $this->id() }})
                });
            });
    </script>
    @endscript
</div>
