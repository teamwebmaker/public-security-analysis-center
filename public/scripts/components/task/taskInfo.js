import { formatDateTime } from '../../helpers.js';
import { formatUser } from '../user/personalInfo.js';

const normalizeTask = (task = {}) => {
  const latest = task.latest_occurrence || task.latestOccurrence || {};
  const status = latest.status || task.status || {};
  const startDate = latest.start_date || latest.startDate || task.start_date;
  const endDate = latest.end_date || latest.endDate || task.end_date;
  const createdAt = task.created_at || task.createdAt;

  const workers =
    (latest.workers || []).map(w => ({ full_name: w.worker_name_snapshot || w.full_name || w.name })).filter(Boolean)
    || [];

  const branchName = task.branch?.name || task.branch_name_snapshot || latest.branch_name_snapshot || '-';
  const companyName = task.branch?.company?.name || '-';
  const serviceTitleKa = task.service?.title?.ka;
  const serviceTitleEn = task.service?.title?.en;
  const serviceSnapshot = task.service_name_snapshot || latest.service_name_snapshot;

  return {
    status,
    startDate,
    endDate,
    createdAt,
    workers: workers.length ? workers : (task.users || []),
    branchName,
    companyName,
    serviceLabel: serviceSnapshot || serviceTitleKa || serviceTitleEn || '-',
    serviceLabelEn: serviceTitleEn,
  };
};

export const TasksTableComponent = (tasks, includeCompany = false) => {
  if (!tasks || tasks.length === 0) {
    return '<p class="text-muted small mb-0 mt-2">სამუშაოები მიუწვდომელია</p>';
  }
  
  return `
    <div class="tasks-section mt-3">
      <h6 class="text-muted fw-bold small mb-2">უახლესი 5 სამუშაო</h6>
      <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
        <table class="table table-sm table-hover table-bordered mb-0">
          <thead class="table-secondary sticky-top">
            <tr>
              <th>სერვისი</th>
              <th>შემსრულებელი</th>
              <th>სტატუსი</th>
              ${includeCompany ? '<th>კომპანია</th> <th>ფილიალი</th>' : ''}
              <th>სამუშაოს განსაზღვრა</th>
              <th>სამუშაოს დაწყება</th>
              <th>სამუშაოს დასრულება</th>
            </tr>
          </thead>
          <tbody>
            ${tasks.map(task => TaskRowComponent(task, includeCompany)).join('')}
          </tbody>
        </table>
      </div>
    </div>
  `;
};

// Single Task Row
const TaskRowComponent = (task, includeCompany) => {
  const normalized = normalizeTask(task);
  const statusName = normalized.status.name;
  const statusClass = 
    statusName === 'completed' ? 'bg-success text-white' : 
    statusName === 'in_progress' ? 'bg-info text-white' : 
    statusName === 'pending' ? 'bg-warning text-dark' : 
    'bg-secondary text-white';
  
  return `
    <tr>
      <td>
        <strong>${normalized.serviceLabel}</strong>
        ${normalized.serviceLabelEn ? `<br><small class="text-muted">${normalized.serviceLabelEn}</small>` : ''}
      </td>
      <td>${formatUser(normalized.workers)}</td>
      <td>
        <span class="badge ${statusClass} px-2 py-1">
          ${normalized.status.display_name || normalized.status.displayName || 'უცნობი'}
        </span>
      </td>
      ${includeCompany ? `
        <td>${normalized.companyName}</td>
        <td>${normalized.branchName}</td>
      ` : ''}
      <td>${formatDateTime(normalized.createdAt)}</td>
      <td>${formatDateTime(normalized.startDate)}</td>
      <td>${formatDateTime(normalized.endDate)}</td>
    </tr>
  `;
};
