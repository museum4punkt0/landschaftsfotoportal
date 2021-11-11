@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('users.edit')</h2>

<form action="{{ route('user.update', $user->id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('users.name')</span>
        <input type="text" name="name" class="form-control" value="{{old('name', $user->name)}}" autofocus />
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('users.email')</span>
        <input type="text" name="email" class="form-control" value="{{old('email', $user->email)}}" />
        <span class="text-danger">{{ $errors->first('email') }}</span>
    </div>
    {{--
    <div class="form-group">
        <span>@lang('users.group')</span>
        <input type="text" name="group" class="form-control" value="{{old('group', $user->group_fk)}}" />
        <span class="text-danger">{{ $errors->first('group') }}</span>
    </div>
    --}}
    
    <div class="form-group">
        <span>@lang('users.group')</span>
        <select name="group" class="form-control" size=1 >
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
