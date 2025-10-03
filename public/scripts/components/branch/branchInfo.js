import {TasksTableComponent} from '../task/taskInfo.js';
// Component: Branches Section
export const BranchesComponent = (branches) => {
  if (!branches || branches.length === 0) {
    return '<p class="text-muted">ფილიალები ვერ მოიძებნა</p>';
  }
  
  return `
    <div class="branches-section p-3">
      <h6 class="fw-bold my-2">ფილიალები</h6>
      ${branches.map(branch => BranchComponent(branch)).join('')}
    </div>
  `;
};

// Component: Branch Item
export const BranchComponent = (branch, parent) => {
  return `
    <div class="branch-item mb-3 p-3 border rounded bg-light shadow-sm">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
          <h6 class="mb-1">
            <i class="bi bi-geo-alt text-primary"></i> ${branch.name}
          </h6>
          <div class="small text-muted">
            <i class="bi bi-geo"></i> ${branch.address}
          </div>
          ${parent 
            ? `<div class="small text-muted mt-1">
                <i class="bi bi-building"></i> მშობელი კომპანია: 
                <strong>${parent.name}</strong>
              </div>` 
            : ''
          }
        </div>
      </div>

      <div class="mt-3">
        ${TasksTableComponent(branch.tasks)}
      </div>
    </div>

  `;
};

