@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">@lang('users.dashboard')</div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @lang('auth.logged_in')
            <br/>
            @lang('users.group'): @lang('users.group_'. $user->group->name)
            
            <!-- Links for user actions, depending on template -->
            @if(Config::get('ui.frontend_layout') == 'landschaftsfotoportal')
                <div class="my-3">
                    <span class="fa-stack fa-2x">
                        <a href="{{ route('item.show.own') }}" title="@lang('items.my_own')">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas fa-images fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                    <span class="fa-stack fa-2x">
                        <a href="{{ route('cart.index') }}" title="@lang('cart.my_own')">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas fa-images fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                    <span class="fa-stack fa-2x">
                        <a href="{{ route('comment.index') }}" title="@lang('comments.my_own')">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas fa-comment fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                    <span class="fa-stack fa-2x">
                        <a href="{{ route('email.change') }}" title="@lang('users.change_email')">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas fa-at fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
