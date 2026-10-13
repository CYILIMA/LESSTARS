import '../css/homepage-slider.css';
import '../css/site-background.css';
import '../css/frontend-scale.css';
import '../css/donations.css';
import '../css/foundation-home.css';
import '../css/theatres.css';
import '../css/theatre-layout-fix.css';
import '../css/event-layout.css';
import '../css/event-detail.css';
import '../css/event-cards.css';
import '../css/event-ratio.css';
import '../css/team-members.css';

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

document.querySelectorAll('[data-team-member-image]').forEach(image => {
  const showFallback = () => {
    image.hidden = true;
    const fallback = image.parentElement?.querySelector('[data-team-member-fallback]');

    if (fallback) {
      fallback.hidden = false;
    }
  };

  image.addEventListener('error', showFallback, { once: true });

  if (image.complete && image.naturalWidth === 0) {
    showFallback();
  }
});

document.querySelectorAll('.amounts button').forEach(button => button.addEventListener('click', () => {
  document.querySelectorAll('.amounts button').forEach(item => item.classList.remove('chosen'));
  button.classList.add('chosen');
  const amount = button.textContent.trim();
  document.querySelector('.donate-button').innerHTML = `Donate ${amount === 'Other' ? 'now' : amount} <span>→</span>`;
}));

document.querySelectorAll('input[type="password"]').forEach(input => {
  const wrapper = document.createElement('span');
  wrapper.className = 'password-field';
  input.before(wrapper);
  wrapper.append(input);

  const toggle = document.createElement('button');
  toggle.type = 'button';
  toggle.className = 'password-toggle';
  toggle.setAttribute('aria-label', 'Show password');
  toggle.setAttribute('aria-pressed', 'false');
  toggle.innerHTML = '<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.8"/></svg>';
  wrapper.append(toggle);

  toggle.addEventListener('click', () => {
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    toggle.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
    toggle.setAttribute('aria-pressed', String(!visible));
    toggle.classList.toggle('is-visible', !visible);
  });
});

document.querySelectorAll('[data-question-list]').forEach(questionList => {
  const template = document.querySelector('[data-question-template]');
  const addButton = document.querySelector('[data-add-question]');
  let index = 0;

  const addQuestion = () => {
    if (!(template instanceof HTMLTemplateElement)) return;

    const question = template.content.firstElementChild.cloneNode(true);
    question.querySelectorAll('[data-field]').forEach(field => {
      const key = field.dataset.field;
      field.name = `questions[${index}][${key}]`;
    });
    question.querySelector('[data-field="type"]')?.addEventListener('change', event => {
      question.querySelector('[data-options-field]').hidden = event.target.value !== 'choice';
    });
    question.querySelector('.remove-question')?.addEventListener('click', () => question.remove());
    questionList.append(question);
    index += 1;
  };

  addButton?.addEventListener('click', addQuestion);
  addQuestion();
});

document.querySelectorAll('[data-thumbnail-editor]').forEach(editor => {
  const input = editor.querySelector('[data-thumbnail-input]');
  const preview = editor.querySelector('[data-thumbnail-preview]');
  const selected = editor.querySelector('[data-thumbnail-selected]');

  input?.addEventListener('change', () => {
    const file = input.files?.[0];

    if (!file) {
      selected.textContent = 'No new image selected';
      return;
    }

    selected.textContent = `Selected: ${file.name}`;

    if (preview instanceof HTMLImageElement) {
      preview.src = URL.createObjectURL(file);
      return;
    }

    const image = document.createElement('img');
    image.className = 'thumbnail-preview';
    image.dataset.thumbnailPreview = '';
    image.alt = `Preview of ${file.name}`;
    image.src = URL.createObjectURL(file);
    preview.replaceWith(image);
  });
});

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

document.documentElement.classList.add('scroll-reveal-ready');
const scrollRevealItems = document.querySelectorAll('[data-scroll-reveal]');

if ('IntersectionObserver' in window) {
  const scrollRevealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) {
        return;
      }

      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.2 });

  scrollRevealItems.forEach(item => scrollRevealObserver.observe(item));
} else {
  scrollRevealItems.forEach(item => item.classList.add('is-visible'));
}
