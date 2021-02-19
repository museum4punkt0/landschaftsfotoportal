<!-- Form for choosing a date using dropdowns -->
{{-- This include accepts two parameters, to be passed as array, e.g.: ['start_year' => 1960, 'end_year' => 1980] --}}
{{-- If params are not set, default values from config/ui.php are used. Please see comments there! --}}
{{-- At last, if config values are not set, sane default values are used, see code below. --}}
<fieldset>
    <div class="form-row">
        <legend class="col-form-label col-1">@lang('common.date_period_start')</legend>
        <div class="form-group col-1">
            <label for="startDay-{{ $cm->column->column_id }}">@lang('common.day')</label>
            <select name="start_day" id="startDay-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                @for ($i = 1; $i <= 31; $i++)
                    <option value="{{ $i }}" @if($i==1) selected @endif>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group col-1">
            <label for="startMonth-{{ $cm->column->column_id }}">@lang('common.month')</label>
            <select name="start_month" id="startMonth-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                <option value="1" selected>@lang('common.january')</option>
                <option value="2">@lang('common.february')</option>
                <option value="3">@lang('common.march')</option>
                <option value="4">@lang('common.april')</option>
                <option value="5">@lang('common.may')</option>
                <option value="6">@lang('common.june')</option>
                <option value="7">@lang('common.july')</option>
                <option value="8">@lang('common.august')</option>
                <option value="9">@lang('common.september')</option>
                <option value="10">@lang('common.october')</option>
                <option value="11">@lang('common.november')</option>
                <option value="12">@lang('common.december')</option>
            </select>
        </div>
        <div class="form-group col-1">
            <label for="startYear-{{ $cm->column->column_id }}">@lang('common.year')</label>
            <select name="start_year" id="startYear-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                @for ($i = ($start_year ?? config('ui.start_year', 1900)); $i <= ($end_year ?? config('ui.end_year', 2020) ?? date('Y')); $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
</fieldset>
<fieldset>
    <div class="form-row">
        <legend class="col-form-label col-1">@lang('common.date_period_end')</legend>
        <div class="form-group col-1">
            <label for="endDay-{{ $cm->column->column_id }}">@lang('common.day')</label>
            <select name="end_day" id="endDay-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                @for ($i = 1; $i <= 31; $i++)
                    <option value="{{ $i }}" @if($i==31) selected @endif>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group col-1">
            <label for="endMonth-{{ $cm->column->column_id }}">@lang('common.month')</label>
            <select name="end_month" id="endMonth-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                <option value="1">@lang('common.january')</option>
                <option value="2">@lang('common.february')</option>
                <option value="3">@lang('common.march')</option>
                <option value="4">@lang('common.april')</option>
                <option value="5">@lang('common.may')</option>
                <option value="6">@lang('common.june')</option>
                <option value="7">@lang('common.july')</option>
                <option value="8">@lang('common.august')</option>
                <option value="9">@lang('common.september')</option>
                <option value="10">@lang('common.october')</option>
                <option value="11">@lang('common.november')</option>
                <option value="12" selected>@lang('common.december')</option>
            </select>
        </div>
        <div class="form-group col-1">
            <label for="endYear-{{ $cm->column->column_id }}">@lang('common.year')</label>
            <select name="end_year" id="endYear-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                @for ($i = ($start_year ?? config('ui.start_year', 1900)); $i <= ($end_year ?? config('ui.end_year', 2020) ?? date('Y')); $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
</fieldset>
