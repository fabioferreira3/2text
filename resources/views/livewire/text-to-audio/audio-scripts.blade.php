<script>
    let currentAudio = null;

    function audioEndedHandler() {
        window.livewire.dispatch('stop-audio');
    }

    window.addEventListener('play-audio', ({
        detail: { id }
    }) => {
        if (currentAudio) {
        // Remove existing listener to avoid duplicate event triggers.
            currentAudio.removeEventListener('ended', audioEndedHandler);
            currentAudio.pause();
            currentAudio.load();
        }

        currentAudio = document.getElementById(id);

        // Add the 'ended' event listener to the current audio clip.
        currentAudio.addEventListener('ended', audioEndedHandler);

        currentAudio.play().catch((error) => {
            console.error("Play error:", error);
        });
    });

    window.addEventListener('stop-audio', () => {
        if (currentAudio) {
            currentAudio.pause();
            currentAudio.load();
        }
    });
</script>