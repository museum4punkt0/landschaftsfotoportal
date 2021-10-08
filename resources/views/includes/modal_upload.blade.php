<!-- Modal for upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">@lang(config('ui.frontend_layout') . '.upload_title')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>@lang(config('ui.frontend_layout') . '.upload_terms')</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="uploadSubmitBtn" data-href="">@lang('common.upload')</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var formId = null;
    // Triggered when upload modal is shown
    $('#uploadModal').on('shown.bs.modal', function(event) {
        // Store the ID of the form
        formId = $(event.relatedTarget).data('form-id');
    });
    
    // Submit and save item
    $('#uploadSubmitBtn').click(function (xhr) {
        xhr.preventDefault();
        $('#uploadModal').modal('hide');
        document.forms[formId].submit();
    });
</script>
