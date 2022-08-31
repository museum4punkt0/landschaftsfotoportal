@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('users.header')</div>
            <div class="card-body">
                
                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('users.name')</th>
                        <th colspan="1">@lang('users.email')</th>
                        <th colspan="1">@lang('users.group')</th>
                        <th colspan="1">@lang('common.created')</th>
                        <th colspan="1">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            {{$user->id}}
                        </td>
                        <td>
                            {{$user->name}}
                        </td>
                        <td>
                            {{$user->email}}
                            <br/>
                            @lang('users.verified'): {{$user->email_verified_at}}
                        </td>
                        <td>
                            @lang('users.group_'. $user->group->name)
                        </td>
                        <td>
                            {{$user->created_at}}
                        </td>
                        <td>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('user.edit', $user) }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('user.destroy', $user) }}"
                                        data-message="@lang('users.confirm_delete', ['name' => $user->name])"
                                        data-title="@lang('users.delete')"
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
            {{ $users->links() }}
        </div>
    </div>
</div>

@endsection
