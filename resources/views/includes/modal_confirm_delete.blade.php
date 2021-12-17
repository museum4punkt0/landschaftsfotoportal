<!-- Modal for confirming of deleting something -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span id="confirmDeleteModalMessage"></span>
            </div>
            <div class="modal-footer">
                <form id="confirmDeleteForm" action="" method="POST">
                    {{ csrf_field() }}
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">@lang('common.delete')</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Triggered when comment modal is shown
    $('#confirmDeleteModal').on('shown.bs.modal', function(event) {
        // Store the URL for the form submit action
        let url = $(event.relatedTarget).data('href');
        $('#confirmDeleteForm').attr('action', url);
        // Set title and message
        $('#confirmDeleteModalTitle').text($(event.relatedTarget).data('title'));
        $('#confirmDeleteModalMessage').text($(event.relatedTarget).data('message'));
    });
</script>
