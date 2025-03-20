const select = document.querySelector('.formulario select');

select.addEventListener('focus', () => {
  select.classList.add('aberto');
});

select.addEventListener('blur', () => {
  select.classList.remove('aberto');
});