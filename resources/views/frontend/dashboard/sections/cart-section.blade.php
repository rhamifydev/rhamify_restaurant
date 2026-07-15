<div class="tab-pane fade" id="v-pills-cart" role="tabpanel" aria-labelledby="v-pills-cart-tab">
    <div class="fp_dashboard_body">
        <h3>Cart</h3>
        <div class="row">
            <div class="col-lg-8">
                <div class="fp__cart_list">
                    <div class="table-responsive">
                        <table>
                            <tbody>
                                <tr>
                                    <th class="fp__pro_img">Image</th>
                                    <th class="fp__pro_name">details</th>
                                    <th class="fp__pro_status">price</th>
                                    <th class="fp__pro_select">quantity</th>
                                    <th class="fp__pro_tk">total</th>
                                    <th class="fp__pro_icon">
                                        <a class="clear_all" href="{{ route('cart.destroy') }}">clear all</a>
                                    </th>
                                </tr>

                                @foreach (Cart::content() as $product)
                                    <tr>
                                        <td class="fp__pro_img">
                                            <img src="{{ asset($product->options->product_info['image']) }}" alt="product"
                                                class="img-fluid w-100">
                                        </td>
                                        <td class="fp__pro_name">
                                            <a href="{{ route('product.show', $product->options->product_info['slug']) }}">{{ $product->name }}</a>
                                            <span>{{ @$product->options->product_size['name'] }}
                                                {{ @$product->options->product_size['price'] ? '(' . currencyPosition(@$product->options->product_size['price']) . ')' : '' }}</span>
                                            @foreach ($product->options->product_options as $option)
                                                <p>{{ $option['name'] }} ({{ currencyPosition($option['price']) }})</p>
                                            @endforeach
                                        </td>
                                        <td class="fp__pro_status">
                                            <h6>{{ currencyPosition($product->price) }}</h6>
                                        </td>
                                        <td class="fp__pro_select">
                                            <div class="quentity_btn">
                                                <button class="btn btn-danger dashboard-cart-decrement"><i
                                                        class="fal fa-minus"></i></button>
                                                <input type="text" class="dashboard-cart-quantity" data-id="{{ $product->rowId }}"
                                                    placeholder="1" value="{{ $product->qty }}" readonly>
                                                <button class="btn btn-success dashboard-cart-increment"><i
                                                        class="fal fa-plus"></i></button>
                                            </div>
                                        </td>
                                        <td class="fp__pro_tk">
                                            <h6 class="dashboard-product-cart-total">
                                                {{ currencyPosition(productTotal($product->rowId)) }}</h6>
                                        </td>
                                        <td class="fp__pro_icon">
                                            <a href="#" class="dashboard-remove-cart-product"
                                                data-id="{{ $product->rowId }}"><i class="far fa-times"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if (Cart::content()->count() === 0)
                                    <tr class="dashboard-empty-cart-row">
                                        <td colspan="6" class="text-center fp__pro_name"
                                            style="width: 100%;display: inline;">Cart is empty!</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="fp__cart_list_footer_button">
                    <h6>total cart</h6>
                    <p>subtotal: <span id="dashboard_cart_subtotal">{{ currencyPosition(cartTotal()) }}</span></p>
                    <p>delivery: <span>$00.00</span></p>
                    <p>discount: <span id="dashboard_cart_discount">
                        @if (isset(session()->get('coupon')['discount']))
                            {{ config('settings.site_currency_icon') }} {{ session()->get('coupon')['discount'] }}
                        @else
                            {{ config('settings.site_currency_icon') }}0
                        @endif
                    </span></p>
                    <p class="total"><span>total:</span> <span id="dashboard_cart_final_total">
                        @if (isset(session()->get('coupon')['discount']))
                            {{ config('settings.site_currency_icon') }} {{ cartTotal() - session()->get('coupon')['discount'] }}
                        @else
                            {{ config('settings.site_currency_icon') }} {{ cartTotal() }}
                        @endif
                    </span></p>
                    <form id="dashboard_coupon_form">
                        <input type="text" id="dashboard_coupon_code" name="code" placeholder="Coupon Code">
                        <button type="submit">apply</button>
                    </form>

                    <div class="dashboard_coupon_card">
                        @if (session()->has('coupon'))
                            <div class="card mt-2">
                                <div class="m-3">
                                    <span><b class="v_coupon_code">Applied Couppon: {{ session()->get('coupon')['code'] }}</b></span>
                                    <span>
                                        <button id="dashboard_destroy_coupon"><i class="far fa-times"></i></button>
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <a class="common_btn" href="{{ route('checkout.index') }}">checkout</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            var dashboardCartTotal = parseInt("{{ cartTotal() }}");

            function updateDashboardCartTotals(response) {
                dashboardCartTotal = response.cart_total;
                $('#dashboard_cart_subtotal').text("{{ config('settings.site_currency_icon') }}" + dashboardCartTotal);
                $('#dashboard_cart_final_total').text("{{ config('settings.site_currency_icon') }}" + response.grand_cart_total);
            }

            function dashboardCartQtyUpdate(rowId, qty, callback) {
                $.ajax({
                    method: 'post',
                    url: '{{ route("cart.quantity-update") }}',
                    data: {
                        'rowId': rowId,
                        'qty': qty
                    },
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        if (callback && typeof callback === 'function') {
                            callback(response);
                        }
                    },
                    error: function(xhr) {
                        hideLoader();
                        toastr.error(xhr.responseJSON?.message || 'Cart could not be updated.');
                    },
                    complete: function() {
                        hideLoader();
                    }
                })
            }

            $('.dashboard-cart-increment').on('click', function() {
                let inputField = $(this).siblings(".dashboard-cart-quantity");
                let rowId = inputField.data("id");
                inputField.val(parseInt(inputField.val()) + 1);

                dashboardCartQtyUpdate(rowId, inputField.val(), function(response) {
                    if (response.status === 'success') {
                        inputField.val(response.qty);
                        inputField.closest("tr").find(".dashboard-product-cart-total").text(
                            "{{ currencyPosition(':productTotal') }}".replace(":productTotal", response.product_total)
                        );
                        updateDashboardCartTotals(response);
                    } else if (response.status === 'error') {
                        inputField.val(response.qty);
                        toastr.error(response.message);
                    }
                });
            });

            $('.dashboard-cart-decrement').on('click', function() {
                let inputField = $(this).siblings(".dashboard-cart-quantity");
                let rowId = inputField.data("id");
                let nextValue = parseInt(inputField.val()) - 1;

                if (nextValue < 1) {
                    return;
                }

                inputField.val(nextValue);
                dashboardCartQtyUpdate(rowId, inputField.val(), function(response) {
                    if (response.status === 'success') {
                        inputField.val(response.qty);
                        inputField.closest("tr").find(".dashboard-product-cart-total").text(
                            "{{ currencyPosition(':productTotal') }}".replace(":productTotal", response.product_total)
                        );
                        updateDashboardCartTotals(response);
                    } else if (response.status === 'error') {
                        inputField.val(response.qty);
                        toastr.error(response.message);
                    }
                });
            });

            $('.dashboard-remove-cart-product').on('click', function(e) {
                e.preventDefault();
                let rowId = $(this).data('id');
                let row = $(this).closest('tr');

                $.ajax({
                    method: 'get',
                    url: '{{ route("cart-product-remove", ":rowId") }}'.replace(":rowId", rowId),
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        row.remove();
                        updateSidebarCart();
                        updateDashboardCartTotals(response);

                        if ($('.dashboard-remove-cart-product').length === 0) {
                            $('.fp__cart_list tbody').append('<tr class="dashboard-empty-cart-row"><td colspan="6" class="text-center fp__pro_name" style="width: 100%;display: inline;">Cart is empty!</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        hideLoader();
                        toastr.error(xhr.responseJSON?.message || 'Item could not be removed.');
                    },
                    complete: function() {
                        hideLoader();
                    }
                })
            })

            $('#dashboard_coupon_form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    method: 'POST',
                    url: '{{ route("apply-coupon") }}',
                    data: {
                        code: $('#dashboard_coupon_code').val(),
                        subtotal: dashboardCartTotal
                    },
                    beforeSend: function() {
                        showLoader()
                    },
                    success: function(response) {
                        $('#dashboard_coupon_code').val("");
                        $('#dashboard_cart_discount').text("{{ config('settings.site_currency_icon') }}" + response.discount);
                        $('#dashboard_cart_final_total').text("{{ config('settings.site_currency_icon') }}" + response.finalTotal);
                        $('.dashboard_coupon_card').html(`<div class="card mt-2">
                            <div class="m-3">
                                <span><b class="v_coupon_code">Applied Couppon: ${response.coupon_code}</b></span>
                                <span>
                                    <button id="dashboard_destroy_coupon"><i class="far fa-times"></i></button>
                                </span>
                            </div>
                        </div>`);
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        hideLoader()
                        toastr.error(xhr.responseJSON?.message || 'Coupon could not be applied.');
                    },
                    complete: function() {
                        hideLoader()
                    }
                })
            })

            $(document).on('click', "#dashboard_destroy_coupon", function() {
                $.ajax({
                    method: 'GET',
                    url: '{{ route("destroy-coupon") }}',
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        $('#dashboard_cart_discount').text("{{ config('settings.site_currency_icon') }}0");
                        $('#dashboard_cart_final_total').text("{{ config('settings.site_currency_icon') }}" + response.grand_cart_total);
                        $('.dashboard_coupon_card').html("");
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        hideLoader()
                        toastr.error(xhr.responseJSON?.message || 'Coupon could not be removed.');
                    },
                    complete: function() {
                        hideLoader();
                    }
                })
            });
        })
    </script>
@endpush
