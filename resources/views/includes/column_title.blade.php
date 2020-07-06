<div class="col-sm-3">
    @unless($cm->getConfigValue('show_title'))
        <div class="font-weight-normal">
            {{ $cm->column->translation->attributes->
                firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
        </div>
    @endunless
</div>
