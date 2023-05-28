@if (session()->has('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif

@if(session('access_granted'))
    <div class="bg-blue-600 font-bold text-lg text-white w-full text-center py-2">
        {{ session('access_granted') }}
    </div>
@endif

