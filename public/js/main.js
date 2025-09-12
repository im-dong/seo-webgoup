function setupImagePreview(inputId, previewId) {
    const imageInput = document.getElementById(inputId);
    const imagePreview = document.getElementById(previewId);

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                imagePreview.src = URL.createObjectURL(file);
            }
        });
    }
}