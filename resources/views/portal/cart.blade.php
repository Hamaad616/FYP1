@extends('layouts.portal')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
@endsection

@section('content')

    <div class="cart-section container">
        <div>
            @if (session()->has('success_message'))
                <div class="alert alert-success">
                    {{ session()->get('success_message') }}
                </div>
            @endif

            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (Cart::count() > 0)

                <h2>{{ Cart::count() }} item(s) in Shopping Cart</h2>

                <div class="cart-table">
                    @foreach (Cart::content() as $item)
                        <div class="cart-table-row">
                            <div class="cart-table-row-left">
                                <a href="{{ url('product_detail', $item->model->id) }}"><img src="{{ asset('uploads/' .$item->model->slug.'.jpg'. $item->model->product_image) }}" alt="item" class="cart-table-img"></a>
                                <div class="cart-item-details">
                                    <div class="cart-table-item"><a href="{{ url('product_detail', $item->model->id) }}">{{ $item->name }}</a></div>
                                    <div class="cart-table-description">{{ $item->model->description }}</div>
                                </div>
                            </div>
                            <div class="cart-table-row-right">
                                <div class="cart-table-actions">
                                    <form action="{{ route('cart.destroy', $item->rowId) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}

                                        <button type="submit" class="cart-options">Remove</button>
                                    </form>

                                    <form action="{{ url('cart.switchToSaveForLater', $item->rowId) }}" method="POST">
                                        {{ csrf_field() }}

                                        <button type="submit" class="cart-options">Save for Later</button>
                                    </form>
                                </div>
                                <div>
                                    <select class="quantity" data-id="{{ $item->rowId }}" data-productQuantity="{{ $item->model->quantity }}">
                                        @for ($i = 1; $i < 5 + 1 ; $i++)
                                            <option {{ $item->qty == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>{{ currentPrice($item->subtotal) }}</div>
                            </div>
                        </div> <!-- end cart-table-row -->
                    @endforeach

                </div> <!-- end cart-table -->

                @if (! session()->has('coupon'))

                    <a href="#" class="have-code">Have a Code?</a>

                    <div class="have-code-container">
                        <form action="{{ url('coupon.store') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="text" name="coupon_code" id="coupon_code">
                            <button type="submit" class="button button-plain">Apply</button>
                        </form>
                    </div> <!-- end have-code-container -->
                @endif

                <div class="cart-totals">
                    <div class="cart-totals-left">
                        Shipping is free because we’re awesome like that. Also because that’s additional stuff I don’t feel like figuring out :).
                    </div>

                    <div class="cart-totals-right">
                        <div>
                            Subtotal <br>
                            @if (session()->has('coupon'))
                                Code ({{ session()->get('coupon')['name'] }})
                                <form action="{{ route('coupon.destroy') }}" method="POST" style="display:block">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <button type="submit" style="font-size:14px;">Remove</button>
                                </form>
                                <hr>
                                New Subtotal <br>
                            @endif
                            Tax ({{config('cart.tax')}}%)<br>
                            <span class="cart-totals-total">Total</span>
                        </div>
                        <div class="cart-totals-subtotal">
                            {{ currentPrice(Cart::subtotal()) }} <br>
{{--                            @if (session()->has('coupon'))--}}
{{--                                -{{ currentPrice($discount) }} <br>&nbsp;<br>--}}
{{--                                <hr>--}}
{{--                                {{ currentPrice($newSubtotal) }} <br>--}}
{{--                            @endif--}}
                            {{ currentPrice(Cart::tax()) }} <br>
                            <span class="cart-totals-total">{{ currentPrice(Cart::total()) }}</span>
                        </div>
                    </div>
                </div> <!-- end cart-totals -->

                <div class="cart-buttons">
                    <a href="{{ url('index_portal') }}" class="button">Continue Shopping</a>
                    <a href="{{ url('checkout.index') }}" class="button-primary">Proceed to Checkout</a>
                </div>

            @else

                <h3>No items in Cart!</h3>
                <div class="spacer"></div>
                <a href="{{ url('index_portal') }}" class="button">Continue Shopping</a>
                <div class="spacer"></div>

            @endif

            @if (Cart::instance('saveForLater')->count() > 0)

                <h2>{{ Cart::instance('saveForLater')->count() }} item(s) Saved For Later</h2>

                <div class="saved-for-later cart-table">
                    @foreach (Cart::instance('saveForLater')->content() as $item)
                        <div class="cart-table-row">
                            <div class="cart-table-row-left">
                                <a href="{{ url('index_portal', $item->model->id) }}"><img src="{{ asset('uploads/' .$item->model->slug.'.jpg'. $item->model->product_image) }}" alt="item" class="cart-table-img"></a>
                                <div class="cart-item-details">
                                    <div class="cart-table-item"><a href="{{ url('index_portal', $item->model->id) }}">{{ $item->name }}</a></div>
                                    <div class="cart-table-description">{{ $item->model->description }}</div>
                                </div>
                            </div>
                            <div class="cart-table-row-right">
                                <div class="cart-table-actions">
                                    <form action="{{ url('saveForLater.destroy', $item->rowId) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}

                                        <button type="submit" class="cart-options">Remove</button>
                                    </form>

                                    <form action="{{ url('saveForLater.switchToCart', $item->rowId) }}" method="POST">
                                        {{ csrf_field() }}

                                        <button type="submit" class="cart-options">Move to Cart</button>
                                    </form>
                                </div>

                                <div>{{ $item->model->currentPrice() }}</div>
                            </div>
                        </div> <!-- end cart-table-row -->
                    @endforeach

                </div> <!-- end saved-for-later -->

            @else

                <h3>You have no items Saved for Later.</h3>

            @endif

        </div>

    </div> <!-- end cart-section ->
<script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function(){
            const classname = document.querySelectorAll('.quantity')

            Array.from(classname).forEach(function(element) {
                element.addEventListener('change', function() {
                    const id = element.getAttribute('data-id')
                    const productQuantity = element.getAttribute('data-productQuantity')

                    axios.patch(`/cart/${id}`, {
                        quantity: this.value,
                        productQuantity: productQuantity
                    })
                    .then(function (response) {
                        // console.log(response);
                        window.location.href = '{{ route('cart.index') }}'
                    })
                    .catch(function (error) {
                        // console.log(error);
                        window.location.href = '{{ route('cart.index') }}'
                    });
                })
            })
        })();
    </script>

    <! Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>



@endsection