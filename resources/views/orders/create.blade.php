<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Place New Order') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('orders.store') }}" x-data="orderForm()" @submit="prepareSubmit">
                @csrf

                <!-- Hidden items container (populated on submit) -->
                <div id="hidden-items"></div>

                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left: Products -->
                    <div class="flex-1 space-y-6">
                        <!-- Ship Selection -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Select Ship') }}</h3>
                            <select name="ship_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">{{ __('-- Choose a ship --') }}</option>
                                @foreach($ships as $ship)
                                    <option value="{{ $ship->id }}" {{ old('ship_id') == $ship->id ? 'selected' : '' }}>{{ $ship->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Products by Vendor -->
                        @foreach($vendors as $vendor)
                            @if($vendor->products->isNotEmpty())
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-100 px-5 py-3">
                                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">{{ $vendor->name }}</h3>
                                </div>
                                <div class="divide-y divide-gray-50">
                                    @foreach($vendor->products as $product)
                                    <div class="flex items-center px-5 py-3 gap-4">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-800 text-sm">{{ $product->name }}</p>
                                            @if($product->category)
                                                <p class="text-xs text-gray-400">{{ $product->category }}</p>
                                            @endif
                                        </div>
                                        <div class="text-sm font-semibold text-indigo-600 w-24 text-right">Rp {{ number_format($product->harga_jual, 0, ",", ".") }}<span class="text-xs font-normal text-gray-400">/{{ $product->unit }}</span></div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="decrement({{ $product->id }})" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full text-gray-600 transition font-bold">−</button>
                                            <input type="number" min="0"
                                                x-model.number="quantities[{{ $product->id }}]"
                                                @change="setQuantity({{ $product->id }}, $event.target.value)"
                                                class="w-14 text-center border-gray-300 rounded-lg text-sm py-1 focus:ring-indigo-500 focus:border-indigo-500" />
                                            <button type="button" @click="increment({{ $product->id }}, {{ $product->harga_jual }})" class="w-7 h-7 flex items-center justify-center bg-indigo-100 hover:bg-indigo-200 rounded-full text-indigo-600 transition font-bold">+</button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach

                        <!-- Notes -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                            <x-input-label for="notes" :value="__('Catatan (opsional)')" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Instruksi khusus...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Pickup Info -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                            <h3 class="font-semibold text-gray-800 mb-3">{{ __('Informasi Pengambilan (opsional)') }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="pickup_date" :value="__('Tanggal Pengambilan')" />
                                    <input type="date" id="pickup_date" name="pickup_date" value="{{ old('pickup_date') }}" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
                                </div>
                                <div>
                                    <x-input-label for="pickup_time" :value="__('Waktu Pengambilan')" />
                                    <input type="time" id="pickup_time" name="pickup_time" value="{{ old('pickup_time') }}" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-input-label for="pickup_location" :value="__('Lokasi Pengambilan')" />
                                <input type="text" id="pickup_location" name="pickup_location" value="{{ old('pickup_location') }}" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Dermaga, pelabuhan, atau lokasi lainnya..." />
                            </div>
                        </div>
                    </div>

                    <!-- Right: Order Summary -->
                    <div class="lg:w-80">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-6">
                            <h3 class="font-semibold text-gray-800 mb-4 text-lg">{{ __('Order Summary') }}</h3>

                            <div class="space-y-2 max-h-64 overflow-y-auto mb-4" x-show="selectedItems.length > 0">
                                <template x-for="item in selectedItems" :key="item.id">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600" x-text="item.name + ' ×' + item.qty"></span>
                                        <span class="font-medium text-gray-800" x-text="'Rp ' + Math.round(item.price * item.qty).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>

                            <div x-show="selectedItems.length === 0" class="text-center py-6 text-gray-400 text-sm">
                                <svg class="mx-auto w-8 h-8 mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                {{ __('No items selected') }}
                            </div>

                            <div class="border-t border-gray-100 pt-4 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-700">{{ __('Total') }}</span>
                                    <span class="text-xl font-bold text-indigo-600" x-text="'Rp ' + Math.round(total).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <button type="submit" :disabled="selectedItems.length === 0" class="mt-5 w-full py-3 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('Place Order') }}
                            </button>
                            <a href="{{ route('orders.index') }}" class="mt-2 block text-center text-sm text-gray-500 hover:text-gray-700">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    function orderForm() {
        const productData = @json($vendors->flatMap(fn($v) => $v->products)->mapWithKeys(fn($p) => [$p->id => ['name' => $p->name, 'price' => (float)$p->harga_jual]]));

        return {
            quantities: {},
            get selectedItems() {
                return Object.entries(this.quantities)
                    .filter(([id, qty]) => qty > 0)
                    .map(([id, qty]) => ({
                        id: parseInt(id),
                        name: productData[id]?.name ?? '',
                        price: productData[id]?.price ?? 0,
                        qty: qty
                    }));
            },
            get total() {
                return this.selectedItems.reduce((sum, i) => sum + i.price * i.qty, 0);
            },
            increment(id) {
                if (!this.quantities[id]) this.quantities[id] = 0;
                this.quantities[id]++;
            },
            decrement(id) {
                if (this.quantities[id] > 0) this.quantities[id]--;
            },
            setQuantity(id, val) {
                const v = parseInt(val);
                this.quantities[id] = isNaN(v) || v < 0 ? 0 : v;
            },
            prepareSubmit(e) {
                const container = document.getElementById('hidden-items');
                container.innerHTML = '';
                let index = 0;
                for (const item of this.selectedItems) {
                    const pid = document.createElement('input');
                    pid.type = 'hidden';
                    pid.name = `items[${index}][product_id]`;
                    pid.value = item.id;
                    container.appendChild(pid);
                    const qty = document.createElement('input');
                    qty.type = 'hidden';
                    qty.name = `items[${index}][quantity]`;
                    qty.value = item.qty;
                    container.appendChild(qty);
                    index++;
                }
            }
        }
    }
    </script>
</x-app-layout>
