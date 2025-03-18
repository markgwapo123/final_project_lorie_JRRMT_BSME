function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    if (dropdown.style.display === 'block') {
      dropdown.style.display = 'none';
    } else {
      dropdown.style.display = 'flex';
    }
  }
  document.addEventListener('DOMContentLoaded', () => {
    filterTable('BSME'); 
  });
  