<section class="flex flex-col justify-between bg-LightBlue w-[10%] md:w-[20%]">
    <div class="flex flex-col items-center md:items-stretch space-y-2 md:space-y-0 bg-MiddelBlue">
        <a href="{{ route('food') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4  text-primary font-bold">
            <img class="w-6 h-6" src="/images/iconFood.png" alt="Food" />
            <span class="hidden md:inline ml-2">FOOD</span>
        </a>
        <a href="{{ route('drink') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4 text-primary font-bold">
            <img class="w-6 h-6" src="/images/iconDrink.png" alt="Drink" />
            <span class="hidden md:inline ml-2">DRINK</span>
        </a>
        <a href="{{ route('snack') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4 text-primary font-bold">
            <img class="w-6 h-6" src="/images/iconSnack.png" alt="Snack" />
            <span class="hidden md:inline ml-2">SNACK</span>
        </a>
    </div>
    <div class="bg-MiddelBlue">
        <a href="{{ route('cart') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4 text-primary font-bold">
            <img class="w-6 h-6" src="/images/iconCart.png" alt="Cart" />
            <span class="hidden md:inline ml-2">CART</span>
        </a>
    </div>
</section>
