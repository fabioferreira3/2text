window.initTypewriter = function (elementId, wordsArray, speedValue) {
    const speed = speedValue || 100;

    let displayedText = "";
    let currentWordIndex = 0;
    let currentCharIndex = 0;
    let direction = 1; // 1 for forward, -1 for backward
    let wait = false;

    const typewriterEl = document.getElementById(elementId);

    // if (!typewriterEl) {
    //     console.log("eita");
    //     return;
    // }

    function updateText() {
        if (wait) {
            return;
        }

        setTimeout(() => {
            displayedText = wordsArray[currentWordIndex].slice(0, currentCharIndex + direction);
            typewriterEl.textContent = displayedText;

            currentCharIndex += direction;
            switchDirectionIfNeeded();
        }, speed);
    }

    function switchDirectionIfNeeded() {
        if (currentCharIndex === wordsArray[currentWordIndex].length && direction === 1) {
            wait = true;
            setTimeout(() => {
                direction = -1;
                wait = false;
                updateText();
            }, speedValue || 100);
        } else if (currentCharIndex === 0 && direction === -1) {
            direction = 1;
            currentWordIndex = (currentWordIndex + 1) % wordsArray.length;
            updateText();
        } else {
            updateText();
        }
    }

    updateText();
};
