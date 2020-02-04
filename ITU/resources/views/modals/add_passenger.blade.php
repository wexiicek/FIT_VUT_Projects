<!--
* ITU Project 2019/2020
* Flight Search (Team xjurig00, xlinka01, xpukan01)
*
* Author of this file: Marian Pukancik (xpukan01)
*
* -->

@section('add_passenger_modal')
    <!-- Modal -->
    <div class="modal fade" id="add_passenger_modal" tabindex="-1" role="dialog" aria-labelledby="add_passenger_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add_passenger_modal_label">New passenger</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('passenger_create', $user->username) }}">
                        @csrf

                        <div class="form-group row">
                            <label for="passenger_name" class="col-md-4 col-form-label text-md-right control-label">Name</label>

                            <div class="col-md-6">
                                <input id="passenger_name" type="text" class="form-control @error('passenger_name') is-invalid @enderror" name="passenger_name" autocomplete="passenger_name" autofocus required>

                                @error('passenger_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="passenger_street" class="col-md-4 col-form-label text-md-right control-label">Street</label>

                            <div class="col-md-6">
                                <input id="passenger_street" type="text" class="form-control @error('passenger_street') is-invalid @enderror" name="passenger_street" autocomplete="passenger_street" autofocus>

                                @error('passenger_street')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="passenger_city" class="col-md-4 col-form-label text-md-right control-label">City</label>

                            <div class="col-md-6">
                                <input id="passenger_city" type="text" class="form-control @error('passenger_city') is-invalid @enderror" name="passenger_city" autocomplete="passenger_city" autofocus>

                                @error('passenger_city')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="passenger_zip" class="col-md-4 col-form-label text-md-right control-label">ZIP Code</label>

                            <div class="col-md-6">
                                <input id="passenger_zip" type="number" min="10000" max="99999" class="form-control @error('passenger_zip') is-invalid @enderror" name="passenger_zip" autocomplete="passenger_zip" autofocus>

                                @error('passenger_zip')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="passenger_state" class="col-md-4 col-form-label text-md-right control-label">State</label>

                            <div class="col-md-6">
                                <input id="passenger_state" type="text" class="form-control @error('passenger_state') is-invalid @enderror" name="passenger_state" autocomplete="passenger_state" autofocus>

                                @error('passenger_state')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Add Passenger
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
