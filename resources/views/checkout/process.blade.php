<x-app-layout>
    <div class="container mx-auto max-w-2xl px-4 pt-16 lg:max-w-7xl lg:px-8 text-sm" x-data="cart()">
        <label>
            <a href="{{ route('checkout.index') }}"
                class="text-sm font-medium text-primary hover:text-accent transition-all delay-[20ms]">
                <span aria-hidden="true"> &larr;</span>
                Return to cart
            </a>
            <h1 class="text-3xl sm:text-4xl tracking-tight font-bold font-heading">Checkout</h1>
        </label>
        <form class="pt-4" @submit.prevent="submitForm" method="POST" x-cloak x-show="cartItems.length > 0">
            @csrf
            <input type="hidden" name="state_province" x-model="state_province" value="">
            <div class="lg:grid grid-cols-12 lg:gap-x-12 xl:gap-x-16">
                <section class="col-span-7 text-text lg:grid mt-10 pt-4 w-full border-t border-gray-200">
                    <!-- Contact Information -->
                    <section>
                        <label class="text-xl">Contact information</label>
                        <div class="mt-4">
                            <div class="grid">
                                <label>Email address</label>
                                <input type="email" name="email" value={{ Auth::user()->email }} required
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                    ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                    focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                            <div class="grid mt-4">
                                <label class="">Phone number</label>
                                <input type="text" name="phone_number" required
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                    ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                    focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                        </div>
                    </section>
                    <!-- Shipping Information -->
                    @php
                        $fullName = auth()->user()->name;
                        $parts = explode(' ', $fullName);
                        $last_name = array_pop($parts);
                        $first_name = implode(' ', $parts);
                    @endphp
                    <section class="lg:grid border-t border-b border-gray-200 mt-6 pt-4 mb-4 pb-6 ">
                        <label class="text-xl">Shipping information</label>
                        <div class="flex mt-4 justify-between">
                            <div class="grid flex-grow">
                                <label class="text-md">First name</label>
                                <input type="text" name="first_name" value="{{ $first_name }}" required
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                    ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                    focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                            <div class="grid ml-4 flex-grow">
                                <label class="text-md">Last name</label>
                                <input type="text" name="last_name" value="{{ $last_name }}" required
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                    ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                    focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                        </div>
                        <label class="text-md mt-4">Address</label>
                        <input type="text" name="address" required
                            class="w-full h-8 text-sm rounded-md font-semibold shadow-sm
                                ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                        <div class="flex mt-4 justify-between">
                            <div class="grid flex-grow">
                                <label class="text-md">City</label>
                                <input type="text" name="city" required
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                        ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                        focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                            <div class="grid ml-4 flex-grow">
                                <label class="text-md">Country</label>
                                <input type="text" name="country" required
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                        ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                        focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                        </div>
                        <div class="flex mt-4 justify-between">
                            <div class="grid flex-grow">
                                <label class="text-md">State / Province</label>
                                <input type="text" name="state_province" x-model="state_province"
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                        ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                        focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                            <div class="grid ml-4 flex-grow">
                                <label class="text-md">Postal code</label>
                                <input type="text" name="postal_code" required
                                    class="h-8 text-sm rounded-md font-semibold shadow-sm
                                        ring-inset ring-gray-300 border-gray-300 hover:bg-gray-50
                                        focus:ring-2 focus:ring-accent focus:border-accent transition-all delay-[20ms]"></input>
                            </div>
                        </div>
                    </section>
                    <!-- Delivery Method -->
                    <section x-cloak>
                        <fieldset class="flex justify-between mt-4">
                            <legend class="text-xl">Delivery Method</legend>
                            <label class="flex flex-1 justify-between cursor-pointer border-2 bg-white p-3 rounded-md"
                                :class="{ 'border-accent': deliveryMethod === 'Standard' }">
                                <input type="radio" name="delivery_method" value="Standard" x-model="deliveryMethod"
                                    class="sr-only" @change="calculateTotalPrice()">
                                <div class="flex flex-col">
                                    <div>Standard</div>
                                    <div>4-10 business days</div>
                                    <div>4.99 €</div>
                                </div>
                                <svg class="h-5 w-5 text-accent" viewBox="0 0 20 20" fill="currentColor"
                                    x-show="deliveryMethod ==='Standard'">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>

                            <label
                                class="flex flex-1 justify-between ml-4 cursor-pointer border-2 bg-white p-3 rounded-md"
                                :class="{ 'border-accent': deliveryMethod === 'Express' }">
                                <input type="radio" name="delivery_method" value="Express" x-model="deliveryMethod"
                                    class="sr-only" @change="calculateTotalPrice()">
                                <div class="flex flex-col">
                                    <div>Express</div>
                                    <div>2-5 business days</div>
                                    <div>12.99 €</div>
                                </div>
                                <svg class="h-5 w-5 text-accent" viewBox="0 0 20 20" fill="currentColor"
                                    x-show="deliveryMethod ==='Express'">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>
                        </fieldset>
                    </section>
                </section>
                <!-- Order Summary -->
                <section class="col-span-5 bg-light-grey h-fit rounded-lg px-4 py-6 sm:p-6 lg:p-8 mt-12" x-cloak>
                    <dl class="text-text">
                        <!-- heading text -->
                        <div class="flex items-baseline justify-between">
                            <dt x-text="`Order Summary`" class="text-xl"></dt>
                            <dd class="text-base" x-text="`Price`"></dd>
                        </div>
                        <!-- items summary -->
                        <div class="mt-6">
                            <template x-for="cartItem in cartItems" :key="cartItem.id">
                                <div class="flex items-center justify-between space-y-2">
                                    <dt x-text="`${cartItem.product.name}`"></dt>
                                    <dd class="font-medium"
                                        x-text="`${Number(cartItem.product.price * cartItem.quantity).toFixed(2)} €`">
                                    </dd>
                                </div>
                            </template>
                        </div>
                        <!-- subtotal -->
                        <div class="flex items-center justify-between border-t border-gray-200 mt-4 pt-4">
                            <dt x-text="`Subtotal:`"></dt>
                            <dd class="font-medium" x-text="`${Number(calculateSubtotalPrice()).toFixed(2)} €`"></dd>
                        </div>
                        <!-- shipping estimate -->
                        <div class="flex items-center justify-between border-t border-gray-200 mt-4 pt-4">
                            <dt x-text="`Shipping Estimate`"></dt>
                            <dd class="font-medium" x-text="`${calculateShippingEstimate()} €`"></dd>
                        </div>
                        <!-- total price -->
                        <div class="flex items-center justify-between border-t border-gray-200 mt-4 pt-4 text-base">
                            <dt x-text="`Order Total:`"></dt>
                            <dd class="font-medium" x-text="`${calculateTotalPrice()} €`"></dd>
                        </div>
                    </dl>
                    <!-- checkout button -->
                    <div class="pt-4">
                        <button type="submit" :disabled="loading"
                            class="flex justify-center gap-2 bg-primary text-base hover:bg-accent text-white font-bold py-2 px-4 rounded-lg min-w-full transition-all delay-[20ms] disabled:brightness-[.85]">
                            Confirm Order <div x-show="loading" class="flex justify-center items-center">
                                <svg aria-hidden="true"
                                    class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-white"
                                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="currentColor" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentFill" />
                                </svg>
                            </div>
                        </button>
                    </div>
                </section>
            </div>
        </form>
    </div>
    <script>
        function cart() {
            return {
                loading: false,
                cartItems: @json($cartItems),
                deliveryMethod: 'Standard',
                state_province: '',

                calculateSubtotalPrice() {
                    let subTotalPrice = 0;
                    this.cartItems.forEach(cartItem => {
                        subTotalPrice += cartItem.product.price * cartItem.quantity;
                    });
                    return Number(subTotalPrice).toFixed(2);
                },

                calculateTotalPrice() {
                    let shippingEstimate = this.calculateShippingEstimate();
                    let totalPrice = parseFloat(this.calculateSubtotalPrice()) + shippingEstimate;
                    return Number(totalPrice).toFixed(2);
                },

                calculateShippingEstimate() {
                    return this.deliveryMethod === "Standard" ? 4.99 : 12.99;
                },

                submitForm() {
                    this.loading = true;

                    let formData = new FormData();
                    formData.append('email', document.querySelector('input[name="email"]').value);
                    formData.append('phone_number', document.querySelector('input[name="phone_number"]').value);
                    formData.append('first_name', document.querySelector('input[name="first_name"]').value);
                    formData.append('last_name', document.querySelector('input[name="last_name"]').value);
                    formData.append('address', document.querySelector('input[name="address"]').value);
                    formData.append('city', document.querySelector('input[name="city"]').value);
                    formData.append('country', document.querySelector('input[name="country"]').value);
                    if (this.state_province) {
                        formData.append('state_province', this.state_province);
                    }
                    formData.append('postal_code', document.querySelector('input[name="postal_code"]').value);
                    formData.append('delivery_method', this.deliveryMethod);

                    console.log(formData);
                    fetch('{{ route('checkout.process') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            if (response.headers.get('Content-Type').includes('application/json')) {
                                return response.json();
                            } else {
                                throw new Error('Non-JSON response received');
                            }
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.href = data.redirectUrl;
                            } else {
                                console.error('Checkout error:', data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error))
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }
        }
    </script>
</x-app-layout>
