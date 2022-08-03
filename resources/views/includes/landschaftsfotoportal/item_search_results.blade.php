                    <!-- Portfolio Grid, Gallery with search results -->
                    @include('includes.item_gallery', [
                        'items' => $items,
                        'limit' => Config::get('ui.search_results'),
                        'heading' => __(config('ui.frontend_layout') . '.search_results_heading'),
                        'subheading' => __(config('ui.frontend_layout') . '.search_results_subheading')
                    ])
                    
                    @if(count($items))
                        <!-- Button with link to map showing the search results-->
                        <div class="container">
                            <a class="btn btn-primary" href="{{ route('item.map', ['source' => 'search']) }}&{{ $query_str }}">@lang('search.results_map')</a>
                        </div>
                    @endif
                    
                    @include('includes.modal_login_request')
                    @include('includes.modal_download')
                    @include('includes.modal_alert')
                    @include('includes.modal_cart_remove')
                    @include('includes.modal_comment_add')
