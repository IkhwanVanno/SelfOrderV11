<header>
    <div
        class="w-full h-[10vh] bg-DarkBlue flex justify-between items-center px-5">
        <a href="">
            <img class="w-8 h-8" src="./images/btnBack.png" alt="Back" />
        </a>
        <h1 class="font-bold text-primary text-lg">SELF ORDER</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-MiddelBlue w-20 h-8 flex items-center justify-center text-primary text-sm rounded">Logout</button>
        </form>
    </div>
</header>