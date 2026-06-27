document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('receiptUpload');
    const uploadZone = document.querySelector('.upload-zone');

    if (fileInput && uploadZone) {
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    // Update layout area to show the uploaded receipt image
                    uploadZone.innerHTML = `
                        <img src="${event.target.result}" class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: contain;">
                        <div class="mt-2 text-muted small">Click to change picture</div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
