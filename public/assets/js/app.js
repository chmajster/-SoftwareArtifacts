document.addEventListener('click', (e) => {
  const trigger = e.target.closest('[data-confirm]');
  if (!trigger) return;
  if (!confirm(trigger.getAttribute('data-confirm') || 'Czy na pewno?')) {
    e.preventDefault();
  }
});

document.querySelectorAll('form').forEach((form) => {
  form.addEventListener('submit', (e) => {
    const required = form.querySelectorAll('[required]');
    for (const field of required) {
      if (!field.value.trim()) {
        e.preventDefault();
        alert('Uzupełnij wymagane pola.');
        field.focus();
        break;
      }
    }
  });
});
