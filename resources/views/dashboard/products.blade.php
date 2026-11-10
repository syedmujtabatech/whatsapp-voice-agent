@extends('layouts.app')

@section('title', 'Product Catalog & Inventory')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-indigo-600">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventory Synchronization</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Product Catalog & Live Stock</h1>
            <p class="text-sm text-slate-500 mt-1">Products synced with the WhatsApp Voice Agent for live stock checks & orders.</p>
        </div>
        <button onclick="openModal()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold flex items-center space-x-2 transition shadow-md shadow-indigo-500/20">
            <i class="fa-solid fa-plus"></i>
            <span>Add New Product</span>
        </button>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $prod)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between group hover:shadow-md transition">
                <div>
                    <div class="relative h-48 bg-slate-100 overflow-hidden">
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/90 backdrop-blur-sm text-slate-700 border border-slate-200">
                            {{ $prod->category }}
                        </span>
                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $prod->stock > 0 ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                            {{ $prod->stock > 0 ? $prod->stock . ' in stock' : 'Out of stock' }}
                        </span>
                    </div>

                    <div class="p-5 space-y-2">
                        <div class="flex items-center justify-between text-xs font-mono text-slate-400">
                            <span>SKU: {{ $prod->sku }}</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm line-clamp-1">{{ $prod->name }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $prod->description }}</p>
                    </div>
                </div>

                <div class="p-5 pt-0 border-t border-slate-100 mt-2 flex items-center justify-between">
                    <div>
                        <span class="text-base font-extrabold text-emerald-600">Rs. {{ number_format($prod->effective_price) }}</span>
                        @if($prod->sale_price && $prod->sale_price < $prod->price)
                            <span class="text-xs text-slate-400 line-through ml-1">Rs. {{ number_format($prod->price) }}</span>
                        @endif
                    </div>
                    <button onclick="editProduct({{ json_encode($prod) }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Edit
                    </button>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 id="modalTitle" class="font-bold text-slate-900 text-base">Add New Product</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="productForm" action="{{ route('admin.products.save') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="font-bold text-slate-700 block mb-1">Product Name</label>
                <input type="text" name="name" id="modal-name" required class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">SKU</label>
                    <input type="text" name="sku" id="modal-sku" required class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Category</label>
                    <input type="text" name="category" id="modal-category" required class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Price (PKR)</label>
                    <input type="number" name="price" id="modal-price" required class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Sale Price</label>
                    <input type="number" name="sale_price" id="modal-sale-price" class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Stock</label>
                    <input type="number" name="stock" id="modal-stock" required class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Image URL</label>
                <input type="url" name="image_url" id="modal-image" class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Description</label>
                <textarea name="description" id="modal-desc" rows="3" class="w-full rounded-xl border-slate-300 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-500/20">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Add New Product';
        document.getElementById('productForm').action = "{{ route('admin.products.save') }}";
        document.getElementById('modal-name').value = '';
        document.getElementById('modal-sku').value = 'SKU-' + Math.floor(Math.random() * 900 + 100);
        document.getElementById('modal-category').value = 'Electronics';
        document.getElementById('modal-price').value = '';
        document.getElementById('modal-sale-price').value = '';
        document.getElementById('modal-stock').value = '15';
        document.getElementById('modal-image').value = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500';
        document.getElementById('modal-desc').value = '';
        document.getElementById('productModal').classList.remove('hidden');
    }

    function editProduct(prod) {
        document.getElementById('modalTitle').textContent = 'Edit Product: ' + prod.name;
        document.getElementById('productForm').action = "/admin/products/" + prod.id + "/save";
        document.getElementById('modal-name').value = prod.name;
        document.getElementById('modal-sku').value = prod.sku;
        document.getElementById('modal-category').value = prod.category;
        document.getElementById('modal-price').value = prod.price;
        document.getElementById('modal-sale-price').value = prod.sale_price || '';
        document.getElementById('modal-stock').value = prod.stock;
        document.getElementById('modal-image').value = prod.image_url || '';
        document.getElementById('modal-desc').value = prod.description || '';
        document.getElementById('productModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>
@endsection
