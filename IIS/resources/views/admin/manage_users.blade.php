@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">


                <button type="button" class="btn btn-success col-md" data-toggle="modal"
                        data-target="#newUserModal">
                    Add New User
                </button>

                <div class="card">
                    <div class="card-header">Tickets</div>
                    <div class="card-body">
                        @if(count($users) > 0)
                            <table>
                                <tr style="border-bottom: 1px solid #999">
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                </tr>
                                @foreach($users as $user)
                                    <tr>

                                        <th>{{$user->id}}</th>
                                        <th>{{$user->username}}</th>
                                        <th>{{$user->firstName ?? ""}}</th>
                                        <th>{{$user->lastName ?? ""}}</th>
                                        <th>{{$user->email}}</th>
                                        <th>
                                            <button type="button" class="btn btn-primary update_user_admin"
                                                    data-toggle="modal"
                                                    data-target="#updateUserInstanceModal" value="{{$user->username}}">
                                                Edit
                                            </button>
                                        </th>
                                        <th>
                                            <button type="button" class="btn btn-danger delete_user_admin"
                                                    data-toggle="modal"
                                                    data-target="#deleteAccountModal" value="{{$user->username}}">Delete
                                            </button>
                                        </th>
                                    </tr>
                                @endforeach

                            </table>

                            <div class="col-md" style="margin-top: 10px">
                                <div class="justify-content-center row">
                                    {{$users->links()}}
                                </div>
                            </div>
                        @else
                            Sorry, there are no users at the moment
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Update User Modal -->
        <div class="modal fade" id="updateUserInstanceModal" tabindex="-1" role="dialog"
             aria-labelledby="updateUserInstanceModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateUserInstanceModalLabel">Update User <span
                                id="modal_username"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('user_profile_update', 'placeholder__') }}" id="update_user_modal_form">
                            @csrf

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    First Name
                                    <input id="firstName" type="text"
                                           class="form-control @error('firstName') is-invalid @enderror"
                                           name="firstName"
                                           value="{{ $user->firstName }}" required
                                           autocomplete="firstName" autofocus>

                                    @error('firstName')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    Last Name
                                    <input id="lastName" type="text"
                                           class="form-control @error('lastName') is-invalid @enderror"
                                           name="lastName"
                                           value="{{ $user->lastName }}" required
                                           autocomplete="lastName" autofocus>

                                    @error('lastName')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    Phone Number
                                    <input id="phoneNumber" type="text"
                                           class="form-control @error('phoneNumber') is-invalid @enderror"
                                           name="phoneNumber"
                                           value="{{ $user->phoneNumber }}" required
                                           autocomplete="phoneNumber">

                                    @error('phoneNumber')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    Role
                                    <select name="user_role" id="user_role" class="form-control">
                                        <option value="admin">Admin</option>
                                        <option value="cashier">Cashier</option>
                                        <option value="director">Director</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        Manages rooms (divided by a comma, empty for all rooms)
                                        <input class="form-control" type="text" name="manages" id="manages" value="{{ old('manages') }}"
                                               placeholder="D105, E112" disabled>
                                    </div>
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-success">Update User Data</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- New User Modal -->
        <div class="modal fade" id="newUserModal" tabindex="-1" role="dialog"
             aria-labelledby="newUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newUserModalLabel">Add New User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('create_user') }}">
                            @csrf

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="firstName" class="control-label">First Name</label>
                                    <input id="firstName" type="text"
                                           class="form-control @error('firstName') is-invalid @enderror"
                                           name="firstName" value="{{ old('firstName') }}" placeholder="Andrew" required
                                           autocomplete="firstName" autofocus>

                                    @error('firstName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="lastName">Last Name</label>
                                    <input id="lastName" type="text"
                                           class="form-control @error('lastName') is-invalid @enderror" name="lastName"
                                           value="{{ old('lastName') }}" placeholder="Smith" 
                                           autocomplete="lastName" autofocus>

                                    @error('lastName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="role" class="control-label">Role</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="admin">Admin</option>
                                        <option value="director">Director</option>
                                        <option value="cashier">Cashier</option>
                                        <option value="user" selected="selected">User</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        Manages rooms (divided by a comma)
                                        <input class="form-control" type="text" name="manages" id="manages" value="{{ old('manages') }}"
                                               placeholder="D105, E112" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">

                                        <label for="username" class="control-label">Username</label>
                                        <input id="username" type="text"
                                               class="form-control @error('username') is-invalid @enderror"
                                               name="username" value="{{ old('username') }}" placeholder="andrewsmith1"
                                               required autocomplete="username">

                                        @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror

                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="email" class="control-label">Email</label>
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ old('email') }}" required autocomplete="email"
                                           placeholder="andrewsmith@email.com">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    Phone Number
                                    <input id="phoneNumber" type="number"
                                           class="form-control @error('email') is-invalid @enderror" name="phoneNumber"
                                           value="{{ old('phoneNumber') }}" autocomplete="phoneNumber"
                                           placeholder="420123123123">

                                    @error('phoneNumber')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="password" class="control-label">Password</label>
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="new-password" placeholder="************">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="col-md-6">
                                    <label for="password-confirm" class="control-label">Confirm Password</label>
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="password_confirmation" required autocomplete="new-password"
                                           placeholder="************">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-success">Add User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account Modal -->
        <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog"
             aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('delete_user_post', '__placeholder__') }}"
                              id="delete_user_modal_form">
                            @csrf

                            <input type="checkbox" required> Do you really want to delete this account? This will also delete all their tickets.


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Delete Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
