import { BranchesComponent } from '../branch/branchInfo.js';

// Component: Companies Section
export const CompaniesComponent = (companies) => {
  if (!companies || companies.length === 0) {
    return '<p class="text-muted">No companies associated</p>';
  }

  return `
    <div class="accordion" id="companiesAccordion">
      ${companies.map((company, idx) => CompanyAccordionItem(company, idx)).join('')}
    </div>
  `;
};

// Component: Company Accordion Item
const CompanyAccordionItem = (company, index) => {
  const collapseId = `company-${index}-collapse`;
  const headingId = `company-${index}-heading`;

  return `
    <div class="accordion-item shadow-sm">
      <h2 class="accordion-header" id="${headingId}">
        <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#${collapseId}" 
                aria-expanded="${index === 0 ? 'true' : 'false'}" 
                aria-controls="${collapseId}">
          <i class="bi bi-building me-2"></i> ${company.name} · ${company.identification_code} · ${company.economic_activity_type.name}
        </button>
      </h2>
      <div id="${collapseId}" 
           class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
           aria-labelledby="${headingId}" 
           data-bs-parent="#companiesAccordion">
        <div class="accordion-body">
          ${BranchesComponent(company.branches)}
        </div>
      </div>
    </div>
  `;
};
