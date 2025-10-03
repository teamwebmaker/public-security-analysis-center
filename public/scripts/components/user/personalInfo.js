export const PersonalInfoComponent = (data) => {
   const {full_name, email, phone, role, is_active } = data
   return `
    <div class="p-3 mb-3 border-bottom">
      <div class="row g-3">
        <div class="col-md-6">
          <strong>სრული სახელი:</strong> ${full_name}
        </div>
        <div class="col-md-6">
          <strong>როლი:</strong> ${role?.display_name ?? '-'}
        </div>
        <div class="col-md-6">
          <strong>ელ.ფოსტა:</strong> ${email ?? '-'}
        </div>
        <div class="col-md-6">
          <strong>ტელეფონი:</strong> ${phone ?? '-'}
        </div>
      </div>
    </div>
  `;
};


/**
 * 
 * @param {Array} users 
 * @returns 
 */
export const formatUser = (users) => {
   if (users === null || users.length <= 0) return `-`;

   if (users.length === 1) return `<strong> ${users[0].full_name} </strong>`;

   if (users.length > 1) return `<select class="form-select form-select-sm"><option selected>სია...</option>${users.map(user => `<option disabled>${user.full_name}</option>`).join('')}</select>`;

}