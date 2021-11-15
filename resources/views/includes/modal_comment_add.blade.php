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
                    <label for="messageTextarea">@lang('comments.message')</label>
                    <textarea id="messageTextarea" name="message" class="form-control" rows=3>
                        {{old('message')}}
                    </textarea>
                    <input type="hidden" id="url" value="" />
                    <span class="text-danger">{{ $errors->first('message') }}</span>
                </div>
                {{ csrf_field() }}
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="commentSubmitBtn" data-href="">@lang('common.save')</button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
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
                // Show alert modal with status message
                $('#alertModalLabel').text('@lang("comments.new")');
                $('#alertModalContent').html('<div class="alert alert-success">' + data.success + '</div>');
                $('#alertModal').modal('show');
                // Close modal dialog
                window.setTimeout(function () {
                    $('#alertModal').modal('hide');
                }, 2500);
            },
            error:function (xhr) {
                // Form validation errors
                if (xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (field, error) {
                        // Render the error messages below each form field
                        $(document).find('[name='+field+']').after('<span class="text-danger">' + error + '</span>')
                    });
                }
                // Laravel error message
                else {
                    $('#commentModal').modal('hide');
                    $('#alertModalLabel').text('@lang("common.laravel_error")');
                    $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                    $('#alertModal').modal('show');
                }
            },
        });
    });
</script>
