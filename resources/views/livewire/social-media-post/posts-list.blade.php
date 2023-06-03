<div class="flex flex-col gap-12">
    <div>
        <h1 class="font-bold text-lg">{{$document->title}}</h1>
    </div>
    <div class="flex flex-col gap-6">
        @if($document->meta['Linkedin'] ?? false)
            <div class="accordion-item">
                <div onclick="toggleAccordion(this)" class='accordion-header h-20 flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                    <img class="h-8" src="{{ Vite::asset('resources/images/linkedin-logo.png') }}">
                </div>
                <div class="accordion-content transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#006193] rounded-b-lg">
                    @livewire('social-media-post.post', [$document, 'Linkedin'])
                </div>
            </div>
        @endif

        @if($document->meta['Twitter'] ?? false)
            <div class="accordion-item">
                <div onclick="toggleAccordion(this)" class='accordion-header h-20 flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                    <img src="{{ asset('images/twitter-logo.svg') }}">
                </div>
                <div class="accordion-content transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#1DA1F2] rounded-b-lg">
                    @livewire('social-media-post.post', [$document, 'Twitter', 'rows' => 6])
                </div>
            </div>
        @endif

        @if($document->meta['Instagram'] ?? false)
            <div class="accordion-item">
                <div onclick="toggleAccordion(this)" class='accordion-header h-20 flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                    <img class="h-8" src="{{ Vite::asset('resources/images/instagram-logo.png') }}">
                </div>
                <div class="accordion-content transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#B92A70] rounded-b-lg">
                    @livewire('social-media-post.post', [$document, 'Twitter', 'rows' => 6])
                </div>
            </div>
        @endif
    </div>
    @if($displayHistory)
    @livewire('common.history-modal', [$document])
    @endif
</div>

<style>
    .accordion-header {
        border-bottom: 1px solid #e5e7eb;
        border-bottom-right-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    .accordion-header.active {
        border-bottom: none !important;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .accordion-content {
        border-top: none !important;
    }
</style>

<script>
    function toggleAccordion(header) {
        var item = header.parentNode;
        var content = item.getElementsByClassName('accordion-content')[0];
        if(content.style.maxHeight && content.style.maxHeight !== '0px'){
        // accordion is currently open, so close it
        content.style.maxHeight = '0px';
        content.style.padding = '0';
        content.style.opacity = '0';
        header.classList.remove("active");
        } else {
        // accordion is currently closed, so open it
        content.style.maxHeight = "600px";
        content.style.padding = '1rem';
        content.style.opacity = '1';
        header.classList.add("active");
        }
    }

    window.onload = function() {
        var contents = document.getElementsByClassName('accordion-content');
        for(var i = 0; i < contents.length; i++) {
            // Initially hide the elements
            contents[i].style.maxHeight = '0px';
            contents[i].style.opacity = '0';
            // Save the total height for later
            contents[i].dataset.maxHeight = contents[i].scrollHeight + 'px';
        }
    };

</script>
