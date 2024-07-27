document.addEventListener("DOMContentLoaded", function() {
    const audioPlayer = document.getElementById('audioPlayer');
    const playlist = document.getElementById('playlist');
    let currentSongIndex = 0;
    let shuffleMode = false;
    let repeatMode = false;
    let songs = Array.from(playlist.children);

    function loadSong(index) {
        if (index >= 0 && index < songs.length) {
            currentSongIndex = index;
            audioPlayer.src = songs[index].dataset.src;
            audioPlayer.play();
        }
    }

    document.getElementById('play').addEventListener('click', () => {
        if (audioPlayer.paused) {
            audioPlayer.play();
        } else {
            audioPlayer.pause();
        }
    });

    document.getElementById('prev').addEventListener('click', () => {
        loadSong((currentSongIndex - 1 + songs.length) % songs.length);
    });

    document.getElementById('next').addEventListener('click', () => {
        loadSong((currentSongIndex + 1) % songs.length);
    });

    document.getElementById('shuffle').addEventListener('click', () => {
        shuffleMode = !shuffleMode;
    });

    document.getElementById('repeat').addEventListener('click', () => {
        repeatMode = !repeatMode;
    });

    audioPlayer.addEventListener('ended', () => {
        if (repeatMode) {
            audioPlayer.play();
        } else if (shuffleMode) {
            loadSong(Math.floor(Math.random() * songs.length));
        } else {
            loadSong((currentSongIndex + 1) % songs.length);
        }
    });

    window.playSong = function(element) {
        const index = songs.indexOf(element);
        loadSong(index);
    };
});
