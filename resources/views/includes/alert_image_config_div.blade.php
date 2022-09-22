{{-- Included in resources/views/admin/item/show.blade.php --}}

@can('show-admin')
    @unless($cm->getConfigValue('image_title_col'))
        <div class="alert alert-danger">
            @lang('items.no_config_for_colmap', ['colmap' => $cm->colmap_id])
            <a href="{{ route('colmap.edit', $cm) . '#image_title_colSelect' }}">
                @lang('colmaps.option_image_title_col_label')
            </a>
        </div>
    @endunless
    @if($cm->getConfigValue('image_link') == 'zoomify')
        @unless($cm->getConfigValue('image_copyright_col'))
            <div class="alert alert-danger">
                @lang('items.no_config_for_colmap', ['colmap' => $cm->colmap_id])
                <a href="{{ route('colmap.edit', $cm) . '#image_copyright_colSelect' }}">
                    @lang('colmaps.option_image_copyright_col_label')
                </a>
            </div>
        @endunless
        @unless($cm->getConfigValue('image_ppi_col'))
            <div class="alert alert-danger">
                @lang('items.no_config_for_colmap', ['colmap' => $cm->colmap_id])
                <a href="{{ route('colmap.edit', $cm) . '#image_ppi_colSelect' }}">
                    @lang('colmaps.option_image_ppi_col_label')
                </a>
            </div>
        @endunless
    @endif
@endcan
