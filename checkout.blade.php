@extends('layouts.master')

@section('title')
    Resumé
@endsection

@section('content')
<script src="https://js.stripe.com/v3/"></script>
<div class="container mt-4">
    @if (session()->has('error_message'))
        <div class="alert alert-warning">
            {{ session()->get('error_message') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" action="{{ route('cart.charge') }}" method="post" id="payment-form">
                                @csrf
                                <div class="form-row">
                                    <label for="mail">Adresse mail</label>
                                    <input class="form-control @error('mail') is-invalid @enderror" id="mail" type="mail" name="mail" required>
                                    @error('mail')<div class="alert alert-danger" style="padding: 5px;">@lang('messages.mail')</div>@enderror
                                </div>
                                <div class="form-row">
                                    <label for="name_on_card">Titulaire de la carte</label>
                                    <input class="form-control @error('name_on_card') is-invalid @enderror" id="name_on_card" type="text" name="name_on_card" required>
                                    @error('name_on_card')<div class="alert alert-danger" style="padding: 5px;">@lang('messages.error')</div>@enderror
                                </div>
                                <div class="form-row">
                                    <label for="address">Adresse</label>
                                    <input class="form-control @error('address') is-invalid @enderror" id="address" type="text" name="address" required>
                                    @error('address')<div class="alert alert-danger" style="padding: 5px;">@lang('messages.error')</div>@enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label for="province">Région</label>
                                        <input class="form-control @error('province') is-invalid @enderror" id="province" type="text" name="province" required>
                                        @error('province')<div class="alert alert-danger" style="padding: 5px;">@lang('messages.error')</div>@enderror
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="float-left" for="postalcode">Code postal</label>
                                        <input class="form-control @error('postalcode') is-invalid @enderror" id="postalcode" type="text" name="postalcode" required>
                                        @error('postalcode')<div class="alert alert-danger" style="padding: 5px;">@lang('messages.error')</div>@enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label for="city">Ville</label>
                                        <input class="form-control @error('city') is-invalid @enderror" id="city" type="text" name="city" required>
                                        @error('city')<div class="alert alert-danger" style="padding: 5px;">@lang('messages.error')</div>@enderror
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label for="telephone">Téléphone</label>
                                        <input class="form-control @error('telephone') is-invalid @enderror" id="phone" type="text" name="telephone" required>
                                        @error('telephone')<div class="alert alert-danger" style="padding: 5px;">@lang('messages.error')</div>@enderror
                                    </div>
                                </div>
                                    <label for="card-element">Informations carte</label>
                                    <div id="card-element"></div>
                                    <div id="card-errors" role="alert"></div>
                                <br>
                        <button id="card-button" class="btn btn-primary btn-block" data-secret="{{ $intent->client_secret }}">Procéder au paiement</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5 mb-4">
            <ul class="list-group mb-3">
            @foreach ($currentCart as $item)
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">{{ $item->name }}</h6>
                <small class="text-muted">{{ $item->options['description'] }}, {{ $item->options['dimension'] }} cm</small>
              </div>
              <span class="text-muted">{{ $item->price }} €</span>
            </li>
            @endforeach
            <li class="list-group-item d-flex justify-content-between">
              <span>Total (EUR)</span>
              <strong>{{ Cart::total()}} €</strong>
            </li>
          </ul>
        </div>
    </div>
</div>

    <script>
        var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
                '::placeholder': {
            color: '#aab7c4'
            }
        },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        var stripe = Stripe('your-public-key');

        var elements = stripe.elements();
        var cardElement = elements.create('card', {
            style: style,
            hidePostalCode : true
        });
        cardElement.mount('#card-element');
        var cardholderName = document.getElementById('name_on_card');
        var cardButton = document.getElementById('card-button');
        var clientSecret = cardButton.dataset.secret;

        var name = document.getElementById('name_on_card');
        var address_line1 = document.getElementById('address');
        var address_city = document.getElementById('city');
        var address_state = document.getElementById('province');
        var address_zip = document.getElementById('postalcode');
        var mail = document.getElementById('mail');
        var phone = document.getElementById('phone');


        var form = document.getElementById('payment-form');
            cardButton.addEventListener('click', function(ev) {
                ev.preventDefault();
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardholderName.value,
                        address: {
                            city: address_city.value,
                            line1: address_line1.value,
                            postal_code: address_zip.value,
                            state: address_state.value,
                            },
                        phone: phone.value,
                        email: mail.value,
                    },
                }
            }).then(function(result) {
                if (result.error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                    } else {
                        form.submit();
                    }
                });
        });

</script>
@endsection
