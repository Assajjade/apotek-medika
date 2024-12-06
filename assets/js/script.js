document.addEventListener('DOMContentLoaded', function () {
    const deleteLinks = document.querySelectorAll('.delete-link');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            if (!confirm('Yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });

    // Smooth scroll to top on logo click (optional)
    const logo = document.querySelector('.header h1');
    if (logo) {
        logo.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});

document.getElementById('add-obat').addEventListener('click', function () {
    const obatContainer = document.getElementById('obat-container');
    const newObat = document.createElement('div');
    newObat.classList.add('flex', 'items-center', 'space-x-4', 'mt-2');

    newObat.innerHTML = `
        <label for="obat_id" class="block text-gray-700">Obat:</label>
        <select name="obat_id[]" required class="p-2 border rounded w-full">
            ${obatContainer.querySelector('select').innerHTML}
        </select>
        <label for="kuantitas" class="block text-gray-700">Kuantitas:</label>
        <input type="number" name="kuantitas[]" required min="1" class="p-2 border rounded w-full">
    `;

    obatContainer.appendChild(newObat);
});
