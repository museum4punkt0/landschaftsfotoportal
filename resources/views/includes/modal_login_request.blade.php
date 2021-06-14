<!-- Modal for requesting login -->
<div class="modal fade" id="requestLoginModal" tabindex="-1" aria-labelledby="requestLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestLoginModalLabel">@lang('Login')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Um diese Funktion zu nutzen, müssen Sie sich anmelden!</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="{{ route('login') }}">@lang('Login')</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
            </div>
        </div>
    </div>
</div>
