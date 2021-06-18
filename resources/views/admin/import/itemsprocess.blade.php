@extends('layouts.app')

@section('content')

@include('includes.modal_alert')

<div class="container">
    <div class="card">
        <div class="card-header">@lang('import.header'): @lang('items.header') ({{ $file_name }})</div>
        <div class="card-body">
            @lang('import.items_count_import', ['count' => $total_items])
            
            <div class="progress my-3" style="height:30px">
                <div class="progress-bar" id="importProgress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="{{ $total_items }}">
                </div>
            </div>
            
            <form action="{{ route('item.index') }}" method="GET" class="form-horizontal">
                {{ csrf_field() }}
                
                <div class="form-group">
                    <label for="importLog">Log</label>
                    <textarea class="form-control" id="importLog" rows="12"></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" id="finishedButton" class="btn btn-primary" disabled>
                        @lang('common.next')
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        // Start import
        importLines(1, 10);
    });
    
    function updateProgressBar(current, text) {
        total = $('#importProgress').attr('aria-valuemax');
        $('#importProgress').attr('aria-valuenow', parseInt(current));
        $('#importProgress').css('width', parseFloat(current/total*100).toString() + '%');
        $('#importProgress').text(text);
    }
    
    function importLines(startLine = 1, limit = 10) {
        $.ajax({
            url: "{{ route('ajax.import.line') }}",
            type: 'get',
            dataType: 'json',
            data: {
                start: startLine,
                limit: limit,
            },
            success: function (data) {
                console.log(data);
                lastLine = parseInt(data.lastLine);
                lastItem = parseInt(data.lastItem);
                totalItems = parseInt(data.totalItems);
                
                updateProgressBar(lastItem, lastItem +'/'+ totalItems);
                // Write status message (if any) to textarea
                if (data.statusMessage) {
                    $('#importLog').append(data.statusMessage + '\n');
                }
                // Start next loop cycle
                if (lastItem < totalItems) {
                    importLines(lastLine + 1, 10);
                }
                // Import finished
                else {
                    $('#alertModalLabel').text('');
                    $('#alertModalContent').html('<div class="alert alert-success">@lang("import.done")</div>');
                    $('#alertModal').modal('show');
                    $('#finishedButton').attr('disabled', false);
                }
                    
            },
            error:function (xhr) {
                console.log(xhr);
                $('#importLog').append('@lang("common.laravel_error")');
                $('#alertModalLabel').text('@lang("common.laravel_error")');
                $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                $('#alertModal').modal('show');
            },
        });
    }
</script>

@endsection
