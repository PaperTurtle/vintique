<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="container">
        <h1>{{ $product->name }}</h1>
        <p>{{ $product->description }}</p>
        <p>Price: ${{ number_format($product->price, 2) }}</p>

        <!-- Images Section -->
        <div class="product-images">
            @foreach ($product->images as $image)
                <img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->alt_text }}"
                    style="width: 100px; height: auto;">
            @endforeach
        </div>
    </div>
</x-app-layout>
