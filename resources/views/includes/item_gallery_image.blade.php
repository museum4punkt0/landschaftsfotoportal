{{-- Included in resources/views/includes/item_gallery.blade.php --}}
{{-- Portfolio gallery image and link --}}

<a class="portfolio-link d-flex justify-content-center" href="{{route('item.show.public', $item->item_id)}}#details">
    <div class="portfolio-hover">
        <div class="portfolio-hover-content text-center">
            <i class="portfolio-caption-heading">
            {{ Str::limit(optional($item->details->firstWhere('column_fk',
                $image_module->config['columns']['caption'] ?? 0))->value_string,
                config('ui.galery_caption_length'), ' (...)') }}
            </i>
        </div>
    </div>
    <div class="img-preview-square" style="background-image: url('{{ str_replace(['(',')'],['\(','\)'], asset('storage/' . Config::get('media.preview_dir') . $item->details->firstWhere('column_fk', $image_module->config['columns']['filename'] ?? 0)->value_string)) }}');">&nbsp;</div>
</a>
