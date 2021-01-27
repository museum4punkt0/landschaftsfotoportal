@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    @include('includes.modal_alert')
    @include('includes.modal_cart_remove')
    
    <!-- Modal for adding a comment -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">@lang('comments.new')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <span>@lang('comments.message')</span>
                        <textarea name="message" class="form-control" rows=3>{{old('message')}}</textarea>
                        <input type="hidden" id="url" value="" />
                        <span class="text-danger">{{ $errors->first('message') }}</span>
                    </div>
                    {{ csrf_field() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="commentSubmitBtn" data-href="">@lang('common.save')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // Triggered when comment modal is shown
        $('#commentModal').on('shown.bs.modal', function(event) {
            // Store the URL for the AJAX request
            var url = $(event.relatedTarget).data('href');
            $('.modal-body #url').val(url);
        });
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Submit and store comment
        $('#commentSubmitBtn').click(function (xhr) {
            xhr.preventDefault();
            var message = $('textarea[name=message]').val();
            
            $.ajax({
                type:'POST',
                url:$('.modal-body #url').val(),
                data:{message:message},
                success:function (data) {
                    $('#commentModal').modal('hide');
                    // Show alert model with status message
                    $('#alertModalLabel').text('@lang("comments.new")');
                    $('#alertModalContent').html('<div class="alert alert-success">' + data.success + '</div>');
                    $('#alertModal').modal('show');
                    // Close modal dialog
                    window.setTimeout(function () {
                        $('#alertModal').modal('hide');
                    }, 2500);
                },
                error:function (xhr) {
                    $.each(xhr.responseJSON.errors, function (field, error) {
                        // Render the error messages below each form field
                        $(document).find('[name='+field+']').after('<span class="text-danger">' + error + '</span>')
                    });
                    $('#alertModal').modal('show');
                },
            });
        });
    </script>
    
    <!-- My Cart Gallery -->
    <section class="page-section bg-light" id="portfolio">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">@lang('cart.my_own')</h2>
                <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
            </div>
            <div class="row">
            @foreach($cart as $item)
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="portfolio-item">
                        <a class="portfolio-link d-flex justify-content-center" href="{{route('item.show.public', $item->item->item_id)}}">
                            <div class="portfolio-hover">
                                <div class="portfolio-hover-content text-center">
                                    <i class="portfolio-caption-heading">
                                    {{ $item->item->details->firstWhere('column_fk', 23)->value_string }}
                                    </i>
                                </div>
                            </div>
                            <img class="img-fluid" src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $item->item->details->firstWhere('column_fk', 13)->value_string .'.jpg') }}" alt="" />
                        </a>
                        <div class="portfolio-caption">
                            <div class="portfolio-caption-heading">
                                {{ $item->item->details->firstWhere('column_fk', 22)->value_string }},
                                {{ $item->item->details->firstWhere('column_fk', 20)->value_string }},
                                {{ $item->item->details->firstWhere('column_fk', 19)->value_string }}
                            </div>
                            <div class="portfolio-caption-subheading text-muted">
                                {{ $item->item->details->firstWhere('column_fk', 5)->value_string }}
                            </div>
                            <div class="my-2" style="font-size: 0.6rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('item.download', $item->item->item_id) }}" title="@lang('common.download')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas fa-download fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#cartRemoveModal" data-href="{{ route('cart.remove', $item->cart_id) }}" title="@lang('cart.remove')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas fa-trash fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#commentModal" data-href="{{ route('comment.store', $item->item->item_id) }}" title="@lang('comments.new')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas fa-comment fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>

@endsection
