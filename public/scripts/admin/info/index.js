import { getById, getOne } from "../../helpers.js";
import { displayUserDetails } from "./userDetails.js";

async function fetchUserDetails(card) {
  const modal = getById('userModal');
  const modalBody = getOne(".modal-body", modal);
  const userId = Number.parseInt(card.dataset.id, 10);
  const summaryUrl = card.dataset.summaryUrl
    || (Number.isInteger(userId) ? `/admin/users/${userId}/dashboard-summary` : null);

  if (!summaryUrl) {
    return;
  }

  modalBody.innerHTML = `
    <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
      <div class="spinner-border text-primary" role="status"><span class="visually-hidden">იტვირთება...</span></div>
    </div>`;
  
  try {
    const res = await fetch(summaryUrl, {
      headers: { Accept: 'application/json' },
    });
    if (!res.ok) throw new Error("Failed to fetch");

    const data = await res.json();
    modalBody.innerHTML = displayUserDetails(data);
  } catch (err) {
    console.error(err);
    modalBody.innerHTML = '<div class="alert alert-danger m-3">მომხმარებლის მონაცემების ჩატვირთვა ვერ მოხერხდა. სცადეთ თავიდან.</div>';
  }
}


document.addEventListener("click", (e) => {
  const card = e.target.closest("[data-id][data-type]");
  
  if (!card || card.dataset.type !== 'user') return;

  fetchUserDetails(card);
});
