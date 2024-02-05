<script>
    let currentAudio = null;

    window.addEventListener('play-audio', ({
        detail: { id }
    }) => {
        if (currentAudio) {
        // Remove existing listener to avoid duplicate event triggers.
            currentAudio.pause();
            currentAudio.load();
        }

        currentAudio = document.getElementById(id);

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
