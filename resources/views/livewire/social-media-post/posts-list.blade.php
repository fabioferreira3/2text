<div class="flex flex-col">
    @include('livewire.common.header', ['icon' => 'hashtag', 'label' => __('social_media.social_media_post')])
    <div class=" mt-12">
        @include('livewire.common.label', ['title' => $document->title])
    </div>
    <div class="flex flex-col mt-4 border rounded-lg p-3">
        <div class="text-zinc-600">
            <span class="font-bold mr-2">Source: </span><span class="italic">{{$document->source}}</span>
        </div>
        @if(isset($document->meta['source_url']))
        <div class="text-zinc-600">
            <span class="font-bold mr-2">Source URL: </span><span class="italic">{{$document->meta['source_url']}}</span>
        </div>
        @endif
        <div class="text-zinc-600">
            <span class="font-bold mr-2">Writing style: </span><span class="italic">{{$document->style ?? 'Default'}}</span>
        </div>
        <div class="text-zinc-600">
            <span class="font-bold mr-2">Writing tone: </span><span class="italic">{{$document->tone ?? 'Default'}}</span>
        </div>
        <div class="text-zinc-600">
            <span class="font-bold mr-2">Other instructions: </span><span class="italic">{{$document->meta['more_instructions'] ?? 'None'}}</span>
        </div>
    </div>

    <div class="flex flex-col gap-6 mt-6">
        @if($document->meta['Linkedin'] ?? false)
        <div class="accordion-item">
            <div onclick="toggleAccordion(this)" class='accordion-header h-20 flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                <img class="h-8" src="{{ Vite::asset('resources/images/linkedin-logo.png') }}">
            </div>
            <div class="accordion-content max-h-0 transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#006193] rounded-b-lg">
                @livewire('social-media-post.post', [$document, 'Linkedin'])
            </div>
        </div>
        @endif

        @if($document->meta['Twitter'] ?? false)
        <div class="accordion-item">
            <div onclick="toggleAccordion(this)" class='accordion-header h-20 flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                <img class="h-20" src="{{ asset('images/twitter-logo.svg') }}">
            </div>
            <div class="accordion-content max-h-0 transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#1DA1F2] rounded-b-lg">
                @livewire('social-media-post.post', [$document, 'Twitter', 'rows' => 6])
            </div>
        </div>
        @endif

        @if($document->meta['Instagram'] ?? false)
        <div class="accordion-item">
            <div onclick="toggleAccordion(this)" class='accordion-header h-20 flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                <img class="h-12" src="{{ Vite::asset('resources/images/instagram-logo.png') }}">
            </div>
            <div class="accordion-content max-h-0 transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#B92A70] rounded-b-lg">
                @livewire('social-media-post.post', [$document, 'Instagram', 'rows' => 6])
            </div>
        </div>
        @endif

        @if($document->meta['Facebook'] ?? false)
        <div class="accordion-item">
            <div onclick="toggleAccordion(this)" class='accordion-header h-20 flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                <img class="h-20" src="{{ Vite::asset('resources/images/facebook-logo.png') }}">
            </div>
            <div class="accordion-content max-h-0 transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#0078F6] rounded-b-lg">
                @livewire('social-media-post.post', [$document, 'Facebook', 'rows' => 6])
            </div>
        </div>
        @endif

        @if($document->meta['TikTok'] ?? false)
        <div class="accordion-item">
            <div onclick="toggleAccordion(this)" class='h-20 accordion-header flex items-center bg-white rounded-t-lg border border-zinc-200 px-4 py-2 cursor-pointer'>
                <img class="h-10" src="{{ Vite::asset('resources/images/tiktok-logo.png') }}">
            </div>
            <div class="accordion-content max-h-0 transition-all duration-300 overflow-hidden p-0 opacity-0 border border-zinc-200 bg-[#000000] rounded-b-lg">
                @livewire('social-media-post.post', [$document, 'TikTok', 'rows' => 6])
            </div>
        </div>
        @endif
    </div>
    @if($displayHistory)
    @livewire('common.history-modal', [$document])
    @endif
</div>

<script>
    function toggleAccordion(header) {
        var item = header.parentNode;
        var content = item.getElementsByClassName('accordion-content')[0];
        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
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
</script>
