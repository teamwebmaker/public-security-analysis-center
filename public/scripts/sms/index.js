import { getById } from "../helpers.js";

(() => {
  document.addEventListener('click', (event) => {
    const trigger = event.target.closest('.js-sms-response-trigger');
    if (!trigger) return;

    const response = trigger.dataset.response ?? '';
    const messageId = trigger.dataset.messageId || '—';

    const contentEl = getById('sms-log-response-content');
    const messageEl = getById('sms-log-response-message-id');

    if (messageEl) messageEl.textContent = messageId;
    if (!contentEl) return;

    if (!response) {
      contentEl.textContent = '';
      return;
    }

    try {
      contentEl.textContent = JSON.stringify(JSON.parse(response), null, 2);
    } catch {
      contentEl.textContent = response;
    }
  });

  // Optional: clear on close to avoid stale content
  const modalEl = getById('sms-log-response-modal');
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', () => {
      const contentEl = getById('sms-log-response-content');
      const messageEl = getById('sms-log-response-message-id');
      if (contentEl) contentEl.textContent = '';
      if (messageEl) messageEl.textContent = '—';
    });
  }
})();
