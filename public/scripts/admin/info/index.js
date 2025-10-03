import { getById, getOne } from "../../helpers.js";
import { displayUserDetails } from "./userDetails.js";

async function fetchEntityDetails(id, type) {
  const modal = getById(`${type}Modal`);
  const modalBody = getOne(".modal-body", modal);
  
  try {
    const res = await fetch(`/admin/${type}s/${id}`);
    if (!res.ok) throw new Error("Failed to fetch");

    const data = await res.json();
    const renderers = {
      user: () => displayUserDetails(data),
      company: () => `
        <h6 class="text-uppercase text-muted mb-3">ზოგადი</h6>
        <p><strong>Name:</strong> ${data.name}</p>
        <p><strong>Identification Code:</strong> ${data.identification_code}</p>
      `,
      branch: () => `
        <h6 class="text-uppercase text-muted mb-3">ფილიალის ინფორმაცია</h6>
        <p><strong>Name:</strong> ${data.name}</p>
        <p><strong>Location:</strong> ${data.location ?? "-"}</p>
      `,
    };

    modalBody.innerHTML = (renderers[type] || (() => `<p>Unsupported type: ${type}</p>`))();
  } catch (err) {
    console.error(err);
    modalBody.innerHTML = `<div class="alert alert-danger">Failed to load ${type} details.</div>`;
  }
}


document.addEventListener("click", (e) => {
  const card = e.target.closest("[data-id][data-type]");
  
  if (!card) return;

  const { id, type } = card.dataset;
  fetchEntityDetails(id, type);
});
