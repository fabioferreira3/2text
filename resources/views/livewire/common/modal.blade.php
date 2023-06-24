<div size="small" id="modal" class="fixed inset-0 w-full h-full z-20 bg-black bg-opacity-30 flex items-center justify-center backdrop-blur-sm">
    <div class="relative overflow-auto max-h-[75%] bg-white rounded-lg shadow-lg w-full sm:w-4/5 {{$sizeConstraints}}">
        <div class="p-4">
            {{$slot}}
        </div>
    </div>
</div>
