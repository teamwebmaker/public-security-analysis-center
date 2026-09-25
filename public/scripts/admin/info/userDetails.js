const escapeHtml = (value) => String(value ?? '—')
  .replaceAll('&', '&amp;')
  .replaceAll('<', '&lt;')
  .replaceAll('>', '&gt;')
  .replaceAll('"', '&quot;')
  .replaceAll("'", '&#039;');

const statusClass = (status) => ({
  completed: 'text-bg-success',
  in_progress: 'text-bg-info',
  pending: 'text-bg-warning',
  on_hold: 'text-bg-secondary',
})[status] || 'text-bg-secondary';

const renderStats = (stats = []) => stats.length
  ? `<div class="row row-cols-2 row-cols-lg-4 g-2 mb-4">
      ${stats.map((stat) => `
        <div class="col">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
              <i class="bi ${escapeHtml(stat.icon)} text-${escapeHtml(stat.tone)}"></i>
              <div class="fs-4 fw-bold mt-1">${escapeHtml(stat.value)}</div>
              <div class="small text-muted">${escapeHtml(stat.label)}</div>
            </div>
          </div>
        </div>
      `).join('')}
    </div>`
  : '';

const renderConnections = (connections = {}) => {
  const items = connections.items || [];

  return `<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0">${escapeHtml(connections.label)}</h6>
      <span class="badge text-bg-light">სულ: ${escapeHtml(connections.total ?? 0)}</span>
    </div>
    ${items.length
      ? `<div class="d-flex flex-wrap gap-2">${items.map((item) => `<span class="badge text-bg-light border text-dark text-wrap text-start">${escapeHtml(item)}</span>`).join('')}</div>`
      : '<p class="small text-muted mb-0">კავშირი მითითებული არ არის.</p>'}
  </section>`;
};

const renderTasks = (tasks = []) => `<section>
  <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
    <h6 class="mb-0">უახლესი 5 საქმე</h6>
    <span class="small text-muted text-end">თითოეული საქმის მიმდინარე მდგომარეობა</span>
  </div>
  ${tasks.length
    ? `<div class="vstack gap-2">${tasks.map((task) => `
        <article class="border rounded-3 bg-white p-3">
          <div class="d-flex justify-content-between gap-2 align-items-start">
            <div>
              <div class="fw-semibold">${escapeHtml(task.service)}</div>
              <div class="small text-muted">#${escapeHtml(task.id)} · ${escapeHtml(task.company)} / ${escapeHtml(task.branch)}</div>
            </div>
            <span class="badge ${statusClass(task.status)} text-nowrap">${escapeHtml(task.status_label)}</span>
          </div>
          <div class="small text-muted mt-2 d-flex flex-wrap gap-3">
            <span>ბოლო ვადა: ${escapeHtml(task.due_date || '—')}</span>
            <span>განახლება: ${escapeHtml(task.updated_at || '—')}</span>
          </div>
          <div class="mt-3">
            <a class="btn btn-sm btn-outline-primary" href="${escapeHtml(task.occurrences_url)}">საქმის გახსნა</a>
          </div>
        </article>
      `).join('')}</div>`
    : '<p class="small text-muted mb-0">საქმეები ვერ მოიძებნა.</p>'}
</section>`;

export const displayUserDetails = (data) => {
  const user = data?.user || {};
  const role = user.role || {};
  const status = user.is_active ? 'აქტიური' : 'არააქტიური';
  const statusClassName = user.is_active ? 'text-bg-success' : 'text-bg-secondary';

  return `<div class="user-details p-3">
    <section class="bg-white border rounded-3 p-3 mb-4">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
          <h5 class="mb-1">${escapeHtml(user.full_name)}</h5>
          <div class="small text-muted">${escapeHtml(role.display_name)}</div>
        </div>
        <span class="badge ${statusClassName}">${status}</span>
      </div>
      <dl class="row small mb-0 mt-3">
        <dt class="col-sm-3 text-muted">ელ.ფოსტა</dt><dd class="col-sm-9">${escapeHtml(user.email)}</dd>
        <dt class="col-sm-3 text-muted">ტელეფონი</dt><dd class="col-sm-9">${escapeHtml(user.phone)}</dd>
        <dt class="col-sm-3 text-muted">რეგისტრაცია</dt><dd class="col-sm-9 mb-0">${escapeHtml(user.created_at)}</dd>
      </dl>
    </section>
    ${renderStats(data?.stats)}
    ${renderConnections(data?.connections)}
    ${renderTasks(data?.tasks)}
    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
      <a class="btn btn-outline-primary" href="${escapeHtml(data?.links?.tasks)}">საქმეების სია</a>
      <a class="btn btn-primary" href="${escapeHtml(data?.links?.edit_user)}">მომხმარებლის რედაქტირება</a>
    </div>
  </div>`;
};
