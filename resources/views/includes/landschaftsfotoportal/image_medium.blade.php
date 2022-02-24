            @if($details->firstWhere('column_fk', $columns['image_filename']))
            <div class="my-5">
                <a href="{{ asset('storage/'. Config::get('media.full_dir') .
                    $item->details->firstWhere('column_fk', $columns['image_filename'])->value_string) }}">
                    <img class="img-fluid" src="{{ asset('storage/'. Config::get('media.medium_dir') .
                        $item->details->firstWhere('column_fk', $columns['image_filename'])->value_string) }}" alt="" />
                </a>
            </div>
            @endif
