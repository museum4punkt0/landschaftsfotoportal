<div class="{{ $div_class }}">
    <label for="{{ $name }}NameInput">{{ $input_label }}</label>
    <input
        type="text"
        id="{{ $name }}NameInput"
        name="{{ $name }}_name"
        aria-describedby="{{ $name }}HelpBlock"
        class="form-control autocomplete @if($errors->has($name)) is-invalid @endif"
        placeholder="{{ $input_placeholder }}"
        value="{{ $taxon_name }}"
    />
    <input type="hidden" id="{{ $name }}Id" name="{{ $name }}" value="{{ $taxon_id }}" />
    
    <small id="{{ $name }}HelpBlock" class="form-text text-muted">
        @lang('taxon.autocomplete_help')
    </small>
    <span class="text-danger">{{ $errors->first($name) }}</span>
</div>

<!-- Script using jQuery UI autocomplete widget -->
<script type="text/javascript">
    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    $(document).ready(function() {
        var nameInput = '{{ $name }}NameInput';
        var idInput = '{{ $name }}Id';

        $('#'+nameInput).autocomplete( {
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
                $('#'+nameInput).val(ui.item.label); // display the selected text
                $('#'+idInput).val(ui.item.value); // set ID of selected taxon
                return false;
            },
            change: function (event, ui) {
                //console.log(ui.item);
                // Check if nothing was selected
                if(ui.item == null) {
                    $('#'+nameInput).val('{{ $null_label }}');
                    $('#'+idInput).val('');
                }
            },
        });
    });
</script>
