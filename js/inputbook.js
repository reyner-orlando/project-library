document.addEventListener('DOMContentLoaded', function() {
document.getElementById('inputBook').addEventListener('submit', function(e) {
  e.preventDefault(); // Cegah form dari reload halaman
  const form = e.target;
  const data = new FormData(form);
  fetch('inputbook.php', {
    method: 'POST',
    body: data
  })
  .then(response => response.text())
  .then(result => {
    alert("Success!");
  })
  .catch(error => {
    document.getElementById('title').innerHTML = 'Terjadi kesalahan: ' + error;
  });
});
});

