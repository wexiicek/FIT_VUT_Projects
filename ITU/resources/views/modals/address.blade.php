<!--
* ITU Project 2019/2020
* Flight Search (Team xjurig00, xlinka01, xpukan01)
*
* Author of this file: Marian Pukancik (xpukan01)
*
* -->
@section('address_modal')
    <!-- Modal -->
    <div class="modal fade" id="address_modal" tabindex="-1" role="dialog" aria-labelledby="address_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="address_modal_label">{{ $user->username }}'s Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_address">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('profile_edit_address', auth()->user()->username) }}" id="address_form">
                        @csrf

                        <div class="form-group row">
                            <label for="street" class="col-md-4 col-form-label text-md-right control-label">Street</label>

                            <div class="col-md-6">
                                <input id="street" type="text" class="form-control @error('street') is-invalid @enderror" name="street" value="{{ $user->street }}"  autocomplete="street" autofocus>

                                @error('street')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city" class="col-md-4 col-form-label text-md-right control-label">City</label>

                            <div class="col-md-6">
                                <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ $user->city }}"  autocomplete="city" autofocus>

                                @error('city')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="zip" class="col-md-4 col-form-label text-md-right control-label">ZIP Code</label>

                            <div class="col-md-6">
                                <input id="zip" type="number" min="10000" max="99999" class="form-control @error('zip') is-invalid @enderror" name="zip" value="{{ $user->zip }}"  autocomplete="zip" autofocus>

                                @error('zip')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="state" class="col-md-4 col-form-label text-md-right control-label">State</label>

                            <div class="col-md-6">
                                <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ $user->state }}"  autocomplete="state" autofocus>

                                @error('state')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Save Credentials
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
