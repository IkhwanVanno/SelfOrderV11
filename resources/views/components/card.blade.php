<div class="border border-black p-4 min-h-[250px] max-h-[300px] flex flex-col items-center gap-3">
    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/ProductSample.jpg') }}" 
         alt="{{ $product->name }}" class="w-[100px] h-[100px] object-cover" />
    <p class="font-semibold text-center">{{ $product->name }}</p>
    <p class="text-center">RP : {{ number_format($product->price, 0, ',', '.') }}</p>
    <div class="flex justify-between items-center w-1/2 gap-4">
        <button onclick="decreaseQuantity({{ $product->id }})" class="decrease-btn">
            <img src="{{ asset('images/iconMinus.png') }}" alt="Kurangi" class="w-5 h-5" />
        </button>
        <p class="text-center quantity-display" id="quantity-{{ $product->id }}">0</p>
        <button onclick="increaseQuantity({{ $product->id }})" class="increase-btn">
            <img src="{{ asset('images/iconPlus.png') }}" alt="Tambah" class="w-5 h-5" />
        </button>
    </div>
</div>