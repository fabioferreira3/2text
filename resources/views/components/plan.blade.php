<div class="flex flex-col w-full md:mt-4 3xl:mt-40 items-center gap-1 bg-gray-100 p-2 rounded-lg">
    <div class="flex flex-col items-center">
        @if(auth()->user()->sparkPlan())
        <div class="text-sm">{{__('menus.current_plan')}}:</div>
        <div class="font-bold text-lg text-gray-700">{{auth()->user()->sparkPlan()->name}}</div>
        @else
        <div class="font-semibold text-gray-700 text-center p-2">{{__('menus.subscribe_to_a_plan')}}
        </div>
        @endif
    </div>
    <a class="flex items-center gap-2 bg-secondary px-4 py-2 rounded-lg text-white" href="/billing">
        <x-icon name="sparkles" width="24" height="24" />
        <div>
            @if(auth()->user()->sparkPlan())
            {{__('menus.upgrade')}}
            @else
            {{__('menus.upgrade_plan')}}
            @endif
        </div>
    </a>
</div>