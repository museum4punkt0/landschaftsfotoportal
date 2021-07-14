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
                <input type="hidden" id="batchSize" name="batch_size" value="{{ Config::get('ui.import_batch_size') }}">
                
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
    $(document).ready(function () {
        let geocoderInteractive = false;
        let geocoderResults = null;
        let geocoderCache = new Map;
        let lastResult = 0;
        let lastLine = 0;
        let lastItem = 0;
        let totalItems = $('#importProgress').attr('aria-valuemax');
        let batchSize = $('#batchSize').val();
        let modalShown = false;
        
        $('#alertModal').on('hidden.bs.modal', function (e) {
            modalShown = false;
        });
        
        // Start import
        run();
        
        function run() {
            if (geocoderInteractive) {
                if (geocoderResults) {
                    console.log(lastResult + '/' + geocoderResults.length + ' geocoder results total');
                    // Geocoder results are bundled for some items
                    if (lastResult < geocoderResults.length) {
                        console.log(geocoderResults[lastResult].results.length + ' geocoder results for item');
                        // More than one result from geocoder -> ask the user
                        if (geocoderResults[lastResult].results.length > 1) {
                            console.log(geocoderResults[lastResult]);
                            // Check cache for previous selections
                            let cacheKey = geocoderResults[lastResult].original.country +
                                geocoderResults[lastResult].original.postcode +
                                geocoderResults[lastResult].original.city +
                                geocoderResults[lastResult].original.street;
                            let selectedResult = checkCache(cacheKey);
                            // No cached result -> ask the user
                            if (selectedResult === false) {
                                showGeocoderModal(geocoderResults[lastResult]);
                            }
                            else {
                                importLatLon(geocoderResults[lastResult].item,
                                    geocoderResults[lastResult].results[selectedResult].lat,
                                    geocoderResults[lastResult].results[selectedResult].lon
                                );
                            }
                        }
                        else {
                            // Exactly one result from geocoder -> just take that one
                            if (geocoderResults[lastResult].results.length == 1) {
                                importLatLon(geocoderResults[lastResult].item,
                                    geocoderResults[lastResult].results[0].lat,
                                    geocoderResults[lastResult].results[0].lon
                                );
                            }
                            // No result at all -> do nothing
                            else {
                                // You should add this place to OpenStreetMap if it really exists
                                importLatLon(geocoderResults[lastResult].item, null, null);
                            }
                        }
                        lastResult ++;
                        return;
                    }
                    // No more items
                    else {
                        console.log('reset results');
                        lastResult = 0;
                        geocoderResults = null;
                    }
                }
            }
            // Import next lines from CSV
            if (lastItem < totalItems) {
                importLines(lastLine + 1, batchSize);
            }
            // Import finished
            else {
                $('#alertModalLabel').text('@lang("import.header")');
                $('#alertModalContent').html('<div class="alert alert-success">@lang("import.done")</div>');
                $('#alertModal').modal('show');
                // Enable "Next" button below form
                $('#finishedButton').attr('disabled', false);
            }
        }
        
        function checkCache(key) {
            if (geocoderCache.has(key)) {
                //alert('cache hit: ' + key + ' => ' + geocoderCache.get(key));
                console.log('cache hit: ' + key + ' => ' + geocoderCache.get(key));
                return geocoderCache.get(key);
            }
            return false;
        }
        
        function fillCache(key, value) {
            //alert('fill cache: ' + value);
            console.log('fill cache: ' + value);
            geocoderCache.set(key, value);
            //console.log(geocoderCache);
        }
        
        function updateProgressBar(current, text) {
            $('#importProgress').attr('aria-valuenow', parseInt(current));
            $('#importProgress').css('width', parseFloat(current/totalItems*100).toString() + '%');
            $('#importProgress').text(text);
        }
        
        function showGeocoderModal(result) {
            // Prepare html result data
            let modalContent = '<div class="alert alert-info">@lang("import.select_location")</div>\n';
            modalContent += '<p><strong>Original: ' + result.original.country + ', ' + result.original.state; 
            modalContent += ', ' + result.original.county + ', ' + result.original.postcode + ', ' + result.original.city; 
            modalContent += ', ' + result.original.street + ', (' + result.original.locality + ')</strong></p>\n';
            modalContent += '<form><div class="form-check">\n'
            modalContent += '<input type="checkbox" id="cache" name="cache" class="form-check-input" data-item="' + result.item + '" value=1 checked />\n';
            modalContent += '<label class="form-check-label" for="cache">@lang("import.geocoder_cache_selected")</label><br/>\n';
            modalContent += '<hr>';
            
            let r = result.results;
            for (let i=0; i < r.length; i++) {
                //console.log(r[i]);
                modalContent += '<input type="radio" id="itemLocation-'+i+'" name="locations" class="form-check-input" data-item="' + result.item + '" data-lat="' + r[i].lat + '" data-lon="' + r[i].lon + '" value="'+i+'" />\n';
                modalContent += '<label class="form-check-label" for="itemLocation-'+i+'">' + r[i].display_name + ')</label>\n';
                modalContent += '<small class="form-text text-muted">(' + r[i].class + ', ' + r[i].lat + '/' + r[i].lon + ')</small><br/>\n';
            }
            modalContent += '<input type="radio" id="itemLocation-999" name="locations" class="form-check-input" data-item="' + result.item + '" data-lat="0" data-lon="0" value="-1" />\n';
            modalContent += '<label class="form-check-label" for="itemLocation-999">@lang("common.none_of_these")</label>\n';
            modalContent += '</div></form>';
            
            // Wait for modal transition being finished
            if (modalShown) {
                console.log('modal is still shown or in transition');
                // TODO: remove this Q&D hack
                alert('Sorry, just click me to continue...');
            }
            
            modalShown = true;
            // Set and show modal
            $('#alertModalLabel').text('@lang("import.header") ID ' + result.item);
            $('#alertModalContent').html(modalContent);
            $('#alertModal').modal('show');
            
            // Triggered when radio changed
            $('input[type=radio][name=locations]').change(function(event) {
                let item = $(this).data('item');
                let lat = $(this).data('lat');
                let lon = $(this).data('lon');
                $('#alertModal').modal('hide');
                
                // Save selection to cache
                if ($('#cache').prop('checked')) {
                    let cacheKey = result.original.country + result.original.postcode + result.original.city + result.original.street;
                    fillCache(cacheKey, event.currentTarget.value);
                }
                
                // Send selected location result to server
                if (event.currentTarget.value == -1) {
                    // None of the results matches given location
                    importLatLon(item, null, null);
                }
                else {
                    importLatLon(item, lat, lon);
                }
            });
        }
        
        function importLatLon(item, lat, lon) {
            $.ajax({
                url: "{{ route('ajax.import.latlon') }}",
                type: 'get',
                dataType: 'json',
                data: {
                    item: item,
                    lat: lat,
                    lon: lon,
                },
                success: function (data) {
                    console.log(data);
                    // Write status message (if any) to textarea
                    if (data.statusMessage) {
                        $('#importLog').append(data.statusMessage);
                    }
                    // Start next loop cycle
                    run();
                },
                error: function (xhr) {
                    console.log(xhr);
                    $('#importLog').append('@lang("common.laravel_error")');
                    $('#alertModalLabel').text('@lang("common.laravel_error")');
                    $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                    $('#alertModal').modal('show');
                },
            });
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
                    geocoderInteractive = Boolean(data.geocoderInteractive);
                    geocoderResults = data.geocoderResults;
                    
                    updateProgressBar(lastItem, lastItem +'/'+ totalItems);
                    // Write status message (if any) to textarea
                    if (data.statusMessage) {
                        $('#importLog').append(data.statusMessage);
                    }
                    // Start next loop cycle
                    run();
                },
                error: function (xhr) {
                    console.log(xhr);
                    $('#importLog').append('@lang("common.laravel_error")');
                    $('#alertModalLabel').text('@lang("common.laravel_error")');
                    $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                    $('#alertModal').modal('show');
                },
            });
        }
    });
</script>

@endsection
