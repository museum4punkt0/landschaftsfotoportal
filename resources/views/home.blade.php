@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')
    <!-- My user dashboard -->
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'dashboard',
        'section_heading' => __(config('ui.frontend_layout') . '.user_profile_heading'),
        'section_subheading' => __(config('ui.frontend_layout') . '.user_profile_subheading'),
    ])

            <div class="container">
                <div class="card">
                    <!--
                    <div class="card-header">@lang('users.profile')</div>
                    -->

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h4 class="text-center mb-5">
                            @lang('auth.logged_in')
                            @lang('users.group'): @lang('users.group_'. $user->group->name)
                        </h4>

                        <!-- Links for user actions, depending on template -->
                        @if(Config::get('ui.frontend_layout') == 'landschaftsfotoportal')
                            <div class="container"><div class="row text-center my-3">
                                <div class="col">
                                    <span class="fa-stack fa-4x">
                                        <a href="{{ route('item.show.own') }}" title="@lang('landschaftsfotoportal.my_items_heading')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas {{ Config::get('ui.icon_items_own') }} fa-stack-1x fa-inverse"></i>
                                        </a>
                                    </span>
                                    <h4 class="my-3">@lang('landschaftsfotoportal.my_items_heading')</h4>
                                </div>
                                <div class="col">
                                    <span class="fa-stack fa-4x">
                                        <a href="{{ route('cart.index') }}" title="@lang('cart.my_own')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas {{ Config::get('ui.icon_cart_add') }} fa-stack-1x fa-inverse"></i>
                                        </a>
                                    </span>
                                    <h4 class="my-3">@lang('cart.my_own')</h4>
                                </div>
                                <div class="col">
                                    <span class="fa-stack fa-4x">
                                        <a href="{{ route('comment.index') }}" title="@lang('comments.my_own')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas {{ Config::get('ui.icon_comment') }} fa-stack-1x fa-inverse"></i>
                                        </a>
                                    </span>
                                    <h4 class="my-3">@lang('comments.my_own')</h4>
                                </div>
                                <div class="col">
                                    <span class="fa-stack fa-4x">
                                        <a href="{{ route('email.change') }}" title="@lang('users.change_email')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas {{ Config::get('ui.icon_email_address') }} fa-stack-1x fa-inverse"></i>
                                        </a>
                                    </span>
                                    <h4 class="my-3">@lang('users.change_email')</h4>
                                </div>
                            </div></div>
                        @endif
                    </div>
                </div>
            </div>

    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')

@endsection
