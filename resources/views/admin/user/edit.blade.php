@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('users.edit')</h2>

<form action="{{ route('user.update', $user->id) }}" method="POST">
    
    <div class="form-group">
        <label for="nameInput">@lang('users.name')</label>
        <input type="text" id="nameInput" name="name" class="form-control"
            value="{{old('name', $user->name)}}" autofocus
        >
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <label for="emailInput">@lang('users.email')</label>
        <input type="text" id="emailInput" name="email" class="form-control"
            value="{{old('email', $user->email)}}"
        >
        <span class="text-danger">{{ $errors->first('email') }}</span>
    </div>
    <div class="form-group">
        <label for="groupSelect">@lang('users.group')</label>
        <select id="groupSelect" name="group" class="form-control" size=1>
            @foreach($groups as $group)
                <option value="{{$group->group_id}}"
                    @if(old('group', $user->group_fk) == $group->group_id) selected @endif>
                    @lang('users.group_'. $group->name)
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('group') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@endsection
