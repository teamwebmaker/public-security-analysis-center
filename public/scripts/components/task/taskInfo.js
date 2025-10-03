import { formatDateTime } from '../../helpers.js';
import { formatUser } from '../user/personalInfo.js';

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
  const statusClass = 
    task.status.name === 'completed' ? 'bg-success text-white' : 
    task.status.name === 'in_progress' ? 'bg-info text-white' : 
    task.status.name === 'pending' ? 'bg-warning text-dark' : 
    'bg-secondary text-white';
  
  return `
    <tr>
      <td>
        <strong>${task.service_name}</strong>
        ${task.service?.title?.en ? `<br><small class="text-muted">${task.service.title.en}</small>` : ''}
      </td>
      <td>${formatUser(task.users)}</td>
      <td>
        <span class="badge ${statusClass} px-2 py-1">
          ${task.status.display_name}
        </span>
      </td>
      ${includeCompany ? `
        <td>${task.branch?.company?.name || '-'}</td>
        <td>${task.branch?.name || '-'}</td>
      ` : ''}
      <td>${formatDateTime(task.created_at)}</td>
      <td>${formatDateTime(task.start_date)}</td>
      <td>${formatDateTime(task.end_date)}</td>
    </tr>
  `;
};
