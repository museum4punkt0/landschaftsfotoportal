<!-- Modal for deleting a comment -->
<div class="modal fade" id="commentDeleteModal" tabindex="-1" aria-labelledby="commentDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentDeleteModalLabel">@lang('comments.delete')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span>@lang('comments.confirm_delete')</span>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <input type="hidden" id="commentDeleteUrl" value="" />
                    <button type="submit" class="btn btn-danger" id="commentDeleteBtn">@lang('common.delete')</button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Triggered when comment modal is shown
    $('#commentDeleteModal').on('shown.bs.modal', function(event) {
        // Store the URL for the AJAX request
        var url = $(event.relatedTarget).data('href');
        $('#commentDeleteUrl').val(url);
    });
    
    // Removing items from comment
    $('#commentDeleteBtn').click(function (xhr) {
        xhr.preventDefault();
        $.ajax({
            type:'POST',
            url:$('#commentDeleteUrl').val(),
            success:function (data) {
                $('#commentDeleteModal').modal('hide');
                // Show alert modal with status message
                $('#alertModalLabel').text('@lang("comments.delete")');
                $('#alertModalContent').html('<div class="alert alert-success">' + data.success + '</div>');
                $('#alertModal').modal('show');
                // Close modal dialog
                window.setTimeout(function () {
                    $('#alertModal').modal('hide');
                    location.reload();
                }, 2500);
            },
            error:function (xhr) {
                $('#commentDeleteModal').modal('hide');
                // Render the Laravel error message
                $('#alertModalLabel').text('@lang("common.laravel_error")');
                $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                $('#alertModal').modal('show');
            },
        });
    });
</script>
