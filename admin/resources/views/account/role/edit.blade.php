@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Role</h2>
                <div>
                    <button onclick="document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Basic</h4>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <form action="{{route('roles.update',$role->id)}}" method="post" id="form">

                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Name</label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" id="product_name" value="{{ $role->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <table class="table table-striped">
                            <thead>
                            <th scope="col" width="1%"><input type="checkbox" name="all_permission"></th>
                            <th scope="col" width="20%">Name</th>
{{--                            <th scope="col" width="1%">Guard</th>--}}
                            <th scope="col" width="20%">Module</th>
                            </thead>

                            @foreach($permissions as $permission)
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               name="permission[{{ $permission->name }}]"
                                               value="{{ $permission->name }}"
                                               class='permission'
                                            {{ in_array($permission->name, $rolePermissions)
                                                ? 'checked'
                                                : '' }}>
                                    </td>
                                    <td>{{ $permission->name }}</td>
{{--                                    <td>{{ $permission->guard_name }}</td>--}}
                                    <td>{{ $permission->module }} >> {{$permission->sub_module}}</td>
                                </tr>
                            @endforeach
                        </table>


                    </form>
                </div>
            </div>

        </div>

    </div>



@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('[name="all_permission"]').on('click', function() {

                if($(this).is(':checked')) {
                    $.each($('.permission'), function() {
                        $(this).prop('checked',true);
                    });
                } else {
                    $.each($('.permission'), function() {
                        $(this).prop('checked',false);
                    });
                }

            });
        });
    </script>
@endsection



