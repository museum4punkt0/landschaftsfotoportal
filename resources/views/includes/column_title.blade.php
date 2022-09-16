{{-- Included in resources/views/item/show.blade.php --}}

@unless($cm->getConfigValue('show_title') === 'hide')
    <div class="col-sm-4 col-xl-3">
        @unless($cm->getConfigValue('show_title') === false)
            <div class="column-title">
                {{ optional($translations->firstWhere('element_fk', $cm->column->translation_fk))->value }}
            </div>
        @endunless
    </div>
@endunless
