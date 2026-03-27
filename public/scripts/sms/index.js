import { getById } from "../helpers.js";

(() => {
  const csrfToken = window.APP_CSRF_TOKEN || '';
  const syncRoute = window.SMS_LOG_SYNC_ROUTE || '';

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

  const syncModalEl = getById('sms-log-sync-modal');
  const syncOptionsWrap = getById('sms-log-sync-options');
  const syncLoader = getById('sms-log-sync-loader');
  const syncError = getById('sms-log-sync-error');

  const setSyncLoading = (isLoading) => {
    if (syncLoader) {
      syncLoader.classList.toggle('d-none', !isLoading);
      syncLoader.classList.toggle('d-flex', isLoading);
    }

    if (!syncOptionsWrap) return;
    syncOptionsWrap
      .querySelectorAll('.js-sms-sync-option')
      .forEach((btn) => {
        btn.disabled = isLoading;
      });
  };

  const postSyncRequest = async (limit) => {
    const response = await fetch(syncRoute, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ limit }),
    });

    let payload = {};
    try {
      payload = await response.json();
    } catch {
      payload = {};
    }

    if (!response.ok) {
      const message = payload?.message || 'სტატუსების განახლება ვერ მოხერხდა.';
      throw new Error(message);
    }

    return payload;
  };

  if (syncOptionsWrap) {
    syncOptionsWrap.addEventListener('click', async (event) => {
      const button = event.target.closest('.js-sms-sync-option');
      if (!button || !syncRoute) return;

      const limit = Number(button.dataset.limit || 0);
      if (![10, 20, 50].includes(limit)) return;

      if (syncError) syncError.textContent = '';
      setSyncLoading(true);

      try {
        await postSyncRequest(limit);

        if (syncModalEl && window.bootstrap?.Modal) {
          const modal = window.bootstrap.Modal.getOrCreateInstance(syncModalEl);
          modal.hide();
        }

        window.location.reload();
      } catch (error) {
        if (syncError) {
          syncError.textContent = error instanceof Error
            ? error.message
            : 'სტატუსების განახლება ვერ მოხერხდა.';
        }
        setSyncLoading(false);
      }
    });
  }

  if (syncModalEl) {
    syncModalEl.addEventListener('hidden.bs.modal', () => {
      setSyncLoading(false);
      if (syncError) syncError.textContent = '';
    });
  }
})();
