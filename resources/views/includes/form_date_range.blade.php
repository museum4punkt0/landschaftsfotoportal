<!-- Form for choosing a date using dropdowns -->
{{-- This include needs two parameters, to be passed as array: start_date[] and end_date: --}}
{{--    e.g.: ['start_date' => [1980, 1, 15], 'end_date' => [2020, 12, 31]] --}}
{{-- This include accepts two additional optional parameters, to be passed as array, --}}
{{--    e.g.: ['start_year' => 1960, 'end_year' => 1980] --}}
{{-- If params are not set, default values from config/ui.php are used. Please see comments there! --}}
{{-- At last, if config values are not set, sane default values are used, see code below. --}}
<fieldset>
    <div class="form-row">
        <legend class="col-form-label col-1">@lang('common.date_period_start')</legend>
        <div class="form-group col-1">
            <label for="startDay-{{ $cm->column->column_id }}">@lang('common.day')</label>
            <select name="start_day" id="startDay-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                @for ($i = 1; $i <= 31; $i++)
                    <option value="{{ $i }}" @if($i == old('start_day'. $cm->column->column_id, $start_date[2])) selected @endif >{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group col-1">
            <label for="startMonth-{{ $cm->column->column_id }}">@lang('common.month')</label>
            <select name="start_month" id="startMonth-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                <option value="1" @if(1 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.january')</option>
                <option value="2" @if(2 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.february')</option>
                <option value="3" @if(3 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.march')</option>
                <option value="4" @if(4 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.april')</option>
                <option value="5" @if(5 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.may')</option>
                <option value="6" @if(6 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.june')</option>
                <option value="7" @if(7 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.july')</option>
                <option value="8" @if(8 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.august')</option>
                <option value="9" @if(9 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.september')</option>
                <option value="10" @if(10 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.october')</option>
                <option value="11" @if(11 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.november')</option>
                <option value="12" @if(12 == old('start_month'. $cm->column->column_id, $start_date[1])) selected @endif >@lang('common.december')</option>
            </select>
        </div>
        <div class="form-group col-1">
            <label for="startYear-{{ $cm->column->column_id }}">@lang('common.year')</label>
            <select name="start_year" id="startYear-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                @for ($i = ($start_year ?? config('ui.start_year', 1900)); $i <= ($end_year ?? config('ui.end_year', 2020) ?? date('Y')); $i++)
                    <option value="{{ $i }}" @if($i == old('start_year'. $cm->column->column_id, $start_date[0])) selected @endif >{{ $i }}</option>
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
                    <option value="{{ $i }}" @if($i == old('end_day'. $cm->column->column_id, $end_date[2])) selected @endif >{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group col-1">
            <label for="endMonth-{{ $cm->column->column_id }}">@lang('common.month')</label>
            <select name="end_month" id="endMonth-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                <option value="1" @if(1 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.january')</option>
                <option value="2" @if(2 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.february')</option>
                <option value="3" @if(3 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.march')</option>
                <option value="4" @if(4 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.april')</option>
                <option value="5" @if(5 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.may')</option>
                <option value="6" @if(6 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.june')</option>
                <option value="7" @if(7 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.july')</option>
                <option value="8" @if(8 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.august')</option>
                <option value="9" @if(9 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.september')</option>
                <option value="10" @if(10 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.october')</option>
                <option value="11" @if(11 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif >@lang('common.november')</option>
                <option value="12" @if(12 == old('end_month'. $cm->column->column_id, $end_date[1])) selected @endif  >@lang('common.december')</option>
            </select>
        </div>
        <div class="form-group col-1">
            <label for="endYear-{{ $cm->column->column_id }}">@lang('common.year')</label>
            <select name="end_year" id="endYear-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" class="form-control">
                @for ($i = ($start_year ?? config('ui.start_year', 1900)); $i <= ($end_year ?? config('ui.end_year', 2020) ?? date('Y')); $i++)
                    <option value="{{ $i }}"  @if($i == old('end_year'. $cm->column->column_id, $end_date[0])) selected @endif >{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
</fieldset>
