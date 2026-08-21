import '../css/homepage-slider.css';
import '../css/site-background.css';
import '../css/frontend-scale.css';
import '../css/donations.css';
import '../css/foundation-home.css';
import '../css/theatres.css';
import '../css/theatre-layout-fix.css';

const menuButton = document.querySelector('.menu-button');
const menu = document.querySelector('.nav-links');
menuButton?.addEventListener('click', () => {
  const open = menu.classList.toggle('open');
  menuButton.setAttribute('aria-expanded', open);
});
document.querySelectorAll('.nav-links a').forEach(link => link.addEventListener('click', () => menu.classList.remove('open')));
document.querySelectorAll('.nav-dropdown-trigger').forEach(trigger => trigger.addEventListener('click', () => {
  const dropdown = trigger.closest('.nav-dropdown');
  const open = dropdown.classList.toggle('is-open');
  trigger.setAttribute('aria-expanded', open);
}));
document.querySelectorAll('.amounts button').forEach(button => button.addEventListener('click', () => {
  document.querySelectorAll('.amounts button').forEach(item => item.classList.remove('chosen'));
  button.classList.add('chosen');
  const amount = button.textContent.trim();
  document.querySelector('.donate-button').innerHTML = `Donate ${amount === 'Other' ? 'now' : amount} <span>→</span>`;
}));

document.querySelectorAll('[data-homepage-slider]').forEach(slider => {
  const slides = [...slider.querySelectorAll('[data-homepage-slide]')];
  const dots = slider.querySelector('.slider-dots');
  let current = 0;

  const showSlide = (next) => {
    slides[current].classList.remove('is-active');
    slides[current].setAttribute('aria-hidden', 'true');
    slides[next].classList.add('is-active');
    slides[next].setAttribute('aria-hidden', 'false');
    current = next;
    dots?.querySelectorAll('button').forEach((dot, index) => dot.classList.toggle('is-active', index === current));
  };

  slides.forEach((_, index) => {
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.classList.toggle('is-active', index === 0);
    dot.setAttribute('aria-label', `Show photo ${index + 1}`);
    dot.addEventListener('click', () => showSlide(index));
    dots?.append(dot);
  });

  slider.querySelector('[data-slider-previous]')?.addEventListener('click', () => showSlide((current - 1 + slides.length) % slides.length));
  slider.querySelector('[data-slider-next]')?.addEventListener('click', () => showSlide((current + 1) % slides.length));
  window.setInterval(() => showSlide((current + 1) % slides.length), 5000);
});
