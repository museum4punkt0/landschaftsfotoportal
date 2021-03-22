<!-- Modal for download -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadModalLabel">Foto in Originalgröße herunterladen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Mit dem Herunterladen akzeptieren Sie die Lizenzbedingungen!</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="#" onClick="$('#downloadModal').modal('hide')">@lang('common.download')</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Triggered when comment modal is shown
    $('#downloadModal').on('shown.bs.modal', function(event) {
        // Store the URL for the download request
        var url = $(event.relatedTarget).data('href');
        $('.modal-footer .btn').attr("href", url);
    });
</script>
