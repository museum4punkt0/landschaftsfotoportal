<div class="{{ $div_class }}">
    <!-- For defining autocomplete search -->
    <input type="text" id='auto_search' class="form-control autocomplete" placeholder="{{ $input_placeholder }}" />
</div>

<!-- Script using jQuery UI autocomplete widget -->
<script type="text/javascript">
    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    $(document).ready(function() {
        $('#auto_search').autocomplete( {
            minLength: 3,
            source: function(request, response) {
                // Fetch data
                $.ajax({
                    url:"{{ $search_url }}",
                    type: 'get',
                    dataType: 'json',
                    data: {
                        _token: CSRF_TOKEN,
                        search: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                // Set selection
                $('#auto_search').val(ui.item.label); // display the selected text
                location.href = ui.item.edit_url;
                return false;
            }
        });
    });
</script>
