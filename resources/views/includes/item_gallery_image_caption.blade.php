{{-- Included in resources/views/includes/item_gallery.blade.php --}}
{{-- Portfolio gallery image caption --}}

<div class="portfolio-caption-heading">
@if(!empty($item->details->firstWhere('column_fk', $image_module->config['columns']['heading-1'] ?? 0)->value_string))
    {{ $item->details->firstWhere('column_fk', $image_module->config['columns']['heading-1'] ?? 0)->value_string }},
@endif
@if(!empty($item->details->firstWhere('column_fk', $image_module->config['columns']['heading-2'] ?? 0)->value_string))
    {{ $item->details->firstWhere('column_fk', $image_module->config['columns']['heading-2'] ?? 0)->value_string }},
@endif
    {{ optional($item->details->firstWhere('column_fk', $image_module->config['columns']['heading-3'] ?? 0))->value_string }}
</div>
<div class="portfolio-caption-subheading text-muted">
    {{ optional($item->details->firstWhere('column_fk', $image_module->config['columns']['subheading'] ?? 0))->value_string }}
</div>
