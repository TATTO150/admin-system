<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg"
    style="background-color: rgba(0, 0, 0, 0.8) ">
        <div class="flex justify-center" style="transition: border-color 0.3s, transform 0.3s;"
                                onmouseover="this.style.transform='scale(1.1)'"
                                onmouseout="this.style.transform='scale(1)'">
            {{ $logo }}
        </div>
        {{ $slot }}
    </div>
</div>
