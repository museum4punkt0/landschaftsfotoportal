<!-- Modal for removing an item from cart -->
<div class="modal fade" id="cartRemoveModal" tabindex="-1" aria-labelledby="cartRemoveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartRemoveModalLabel">@lang('cart.remove')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span>@lang('cart.confirm_remove')</span>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <input type="hidden" id="cartRemoveUrl" value="" />
                    <button type="submit" class="btn btn-danger" id="cartRemoveBtn">@lang('common.delete')</button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Triggered when cart modal is shown
    $('#cartRemoveModal').on('shown.bs.modal', function(event) {
        // Store the URL for the AJAX request
        var url = $(event.relatedTarget).data('href');
        $('#cartRemoveUrl').val(url);
    });
    
    // Removing items from cart
    $('#cartRemoveBtn').click(function (xhr) {
        xhr.preventDefault();
        $.ajax({
            type:'POST',
            url:$('#cartRemoveUrl').val(),
            success:function (data) {
                $('#cartRemoveModal').modal('hide');
                // Show alert modal with status message
                $('#alertModalLabel').text('@lang("cart.remove")');
                $('#alertModalContent').html('<div class="alert alert-success">' + data.success + '</div>');
                $('#alertModal').modal('show');
                // Close modal dialog
                window.setTimeout(function () {
                    $('#alertModal').modal('hide');
                    location.reload();
                }, 2500);
            },
            error:function (xhr) {
                $('#cartRemoveModal').modal('hide');
                // Render the Laravel error message
                $('#alertModalLabel').text('@lang("common.laravel_error")');
                $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                $('#alertModal').modal('show');
            },
        });
    });
    
    // Adding items to cart
    $('.cartAddBtn').click(function (xhr) {
        xhr.preventDefault();
        
        $.ajax({
            type:'POST',
            url:$(this).data('href'),
            success:function (data) {
                // Show alert model with status message
                $('#alertModalLabel').text('@lang("cart.add")');
                $('#alertModalContent').html('<div class="alert alert-success">' + data.success + '</div>');
                $('#alertModal').modal('show');
                // Close modal dialog
                window.setTimeout(function () {
                    $('#alertModal').modal('hide');
                    location.reload();
                }, 2500);
            },
            error:function (xhr) {
                // Render the Laravel error message
                $('#alertModalLabel').text('@lang("common.laravel_error")');
                $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                $('#alertModal').modal('show');
            },
        });
    });
</script>
