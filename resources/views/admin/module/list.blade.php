@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('modules.header')</div>
            <div class="card-body">
                <a href="{{route('module.new')}}" class="btn btn-primary">@lang('modules.new')</a>

                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="1">@lang('common.description')</th>
                        <th colspan="1">@lang('modules.type')</th>
                        <th colspan="1">@lang('modules.position')</th>
                        <th colspan="1">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($modules as $module)
                    <tr>
                        <td>
                            {{ $module->module_instance_id }}
                        </td>
                        <td>
                            {{ $module->name }}
                        </td>
                        <td>
                            {{ $module->description }}
                        </td>
                        <td>
                            {{ $module->template->name }}<br>({{ $module->template->description }})
                        </td>
                        <td>
                            {{ $module->position }}
                        </td>
                        <td>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('module.edit', $module) }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('module.destroy', $module) }}"
                                        data-message="@lang('modules.confirm_delete', ['name' => $module->name])"
                                        data-title="@lang('modules.delete')"
                                        title="@lang('common.delete')"
                                    >
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_delete') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                </table>
                </div>
            </div>
        @else
            <div class="card-body">
                <h3>You need to log in. <a href="{{url()->current()}}/login">Click here to login</a></h3>
            </div>
        @endif
        <div class="card-footer">
            {{ $modules->links() }}
        </div>
    </div>
</div>

@endsection
