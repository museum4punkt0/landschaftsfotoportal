@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')
    <!-- My user dashboard -->
    <section class="page-section bg-light" id="dashboard">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">@lang('users.profile')</h2>
                <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
            </div>

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
                                        <i class="fas {{ Config::get('ui.icon_items_own') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('cart.index') }}" title="@lang('cart.my_own')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_cart_add') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('comment.index') }}" title="@lang('comments.my_own')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_comment') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('email.change') }}" title="@lang('users.change_email')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_email_address') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
