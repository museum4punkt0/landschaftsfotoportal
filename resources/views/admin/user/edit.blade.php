@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('users.edit')</h2>

<form action="{{ route('user.update', $user->id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('users.name')</span>
        <input type="text" name="name" class="form-control" value="{{old('name', $user->name)}}" />
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('users.email')</span>
        <input type="text" name="email" class="form-control" value="{{old('email', $user->email)}}" />
        <span class="text-danger">{{ $errors->first('email') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('users.group')</span>
        <input type="text" name="group" class="form-control" value="{{old('group', $user->group_fk)}}" />
        <span class="text-danger">{{ $errors->first('group') }}</span>
    </div>
    
    {{--
    <div class="form-group">
        <span>@lang('user.parent')</span>
        <select name="parent" class="form-control" size=1 >
            <option value="">@lang('common.root')</option>
            @foreach($taxa as $t)
                @unless($t->valid_name)
                    <option value="{{$t->id}}"
                        @if(old('parent', $user->parent_fk) == $t->id) selected @endif>
                        @for ($i = 0; $i < $t->depth; $i++)
                            |___
                        @endfor
                        {{$t->name}} {{$t->author}} ({{$t->native_name}})
                    </option>
                @endunless
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    --}}
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@endsection
