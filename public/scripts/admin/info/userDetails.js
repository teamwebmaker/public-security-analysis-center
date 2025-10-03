import { PersonalInfoComponent } from "../../components/user/personalInfo.js";
import { CompaniesComponent } from "../../components/company/companiesInfo.js";
import { BranchComponent, BranchesComponent } from "../../components/branch/branchInfo.js";
import { TasksTableComponent } from "../../components/task/taskInfo.js";

export const displayUserDetails = (data) => {
  const role = data?.role?.name;

  // Role-based renderers
  const renderers = {
    company_leader: () => `
      ${PersonalInfoComponent(data)}
      ${CompaniesComponent(data.companies)}
    `,
    responsible_person: () => `
      ${PersonalInfoComponent(data)}   
      ${ data.branches?.map(branch => BranchComponent(branch, branch.company)).join('') }

    `,

    worker: () => `
     ${PersonalInfoComponent(data)}
     <div class="px-3"> 
      ${TasksTableComponent(data.tasks, true)}
     </div>
    `
  };

  // Fallback if role not recognized
  const renderContent = renderers[role] || (() => `
    ${PersonalInfoComponent(data)}
    <p class="text-muted">No additional details available for role: ${role}</p>
  `);

  // Return the rendered HTML
  return `
    <div class="user-details">
      ${renderContent()}
    </div>
  `;
};
