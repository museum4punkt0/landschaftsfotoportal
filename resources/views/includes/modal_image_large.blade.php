<!-- Modal for showing a large preview of an image -->
<div class="modal fade" id="imageLargeModal" tabindex="-1" aria-labelledby="imageLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageLargeModalLabel">@lang('items.image_preview')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="imageLargeModalContent">
                <img class="img-fluid mx-auto d-block" src="" id="imageSource">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Triggered when modal is shown
    $('#imageLargeModal').on('shown.bs.modal', function(event) {
        // Store the source for the image file
        let url = $(event.relatedTarget).data('img-source');
        $('#imageSource').attr('src', url);
        //$('#imageLargeModal').modal('handleUpdate')
    });
</script>
