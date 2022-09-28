<div class="{{ $div_class }}">
    @include('includes.column_label')
    <div class="form-row">
        <div class="col">
            <input
                type="text"
                id="fieldsInput-{{ $column }}"
                name="fields_name[{{ $column }}]"
                aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                class="form-control autocomplete @if($errors->has('fields.' . $cm->column->column_id)) is-invalid @endif"
                data-column="{{ $cm->column->column_id }}"
                data-type="relation"
                placeholder="{{ $input_placeholder }}"
                value="{{ $item_title }}"
            />
            <input type="hidden" id="fieldsHiddenInput-{{ $column }}" name="fields[{{ $column }}]" value="{{ $item_id }}" />
        </div>
        <div class="col-auto">
            <a href="{{ route('item.new', ['item_type' => $cm->getConfigValue('relation_item_type'), 'msg' => 'new_related']) }}"
                target="_blank" class="btn btn-primary">@lang('common.new')</a>
        </div>
    </div>
    
    @include('includes.form_input_help')
    <span class="text-danger">{{ $errors->first('fields.' . $cm->column->column_id) }}</span>
</div>

<!-- Script using jQuery UI autocomplete widget -->
<script type="text/javascript">
    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    $(document).ready(function() {
        var nameInput = '#fieldsInput-{{ $column }}';
        var idInput = '#fieldsHiddenInput-{{ $column }}';

        $(nameInput).autocomplete( {
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
                $(nameInput).val(ui.item.label); // display the selected text
                $(idInput).val(ui.item.value); // set ID of selected taxon
                return false;
            },
            change: function (event, ui) {
                //console.log(ui.item);
                // Check if nothing was selected
                if(ui.item == null) {
                    $(nameInput).val('{{ $null_label }}');
                    $(idInput).val('');
                }
            },
        });
    });
</script>
