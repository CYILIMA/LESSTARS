const menuButton = document.querySelector('.menu-button');
const menu = document.querySelector('.nav-links');
menuButton?.addEventListener('click', () => {
  const open = menu.classList.toggle('open');
  menuButton.setAttribute('aria-expanded', open);
});
document.querySelectorAll('.nav-links a').forEach(link => link.addEventListener('click', () => menu.classList.remove('open')));
document.querySelectorAll('.amounts button').forEach(button => button.addEventListener('click', () => {
  document.querySelectorAll('.amounts button').forEach(item => item.classList.remove('chosen'));
  button.classList.add('chosen');
  const amount = button.textContent.trim();
  document.querySelector('.donate-button').innerHTML = `Donate ${amount === 'Other' ? 'now' : amount} <span>→</span>`;
}));
