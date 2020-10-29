@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @lang('auth.logged_in')
                    <br/>
                    @lang('users.group'): @lang('users.group_'. $user->group->name)
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
