<section class="flex flex-col justify-between bg-LightBlue w-[10%] md:w-[20%]">
    <div class="flex flex-col items-center md:items-stretch space-y-2 md:space-y-0 bg-MiddelBlue">
        <a href="{{ route('order') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4  text-primary font-bold">
            <img class="w-6 h-6" src="/images/Order.png" alt="Order" />
            <span class="hidden md:inline ml-2">ORDER</span>
        </a>
        <a href="{{ route('payment') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4 text-primary font-bold">
            <img class="w-6 h-6" src="/images/Payment.png" alt="Payment" />
            <span class="hidden md:inline ml-2">PAYMENT</span>
        </a>
        <a href="{{ route('product') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4 text-primary font-bold">
            <img class="w-6 h-6" src="/images/product.png" alt="Product" />
            <span class="hidden md:inline ml-2">PRODUCT</span>
        </a>
        <a href="{{ route('user') }}" class="flex flex-col md:flex-row items-center justify-center md:justify-start p-2 md:p-4 text-primary font-bold">
            <img class="w-6 h-6" src="/images/Users.png" alt="Users" />
            <span class="hidden md:inline ml-2">USERS</span>
        </a>
    </div>
</section>
