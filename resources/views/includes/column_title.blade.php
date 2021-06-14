{{-- Included in resources/views/item/show.blade.php --}}

@unless($cm->getConfigValue('show_title') == 'hide')
    <div class="col-sm-3">
        @unless($cm->getConfigValue('show_title'))
            <div class="font-weight-normal">
                {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }}
            </div>
        @endunless
    </div>
@endunless
