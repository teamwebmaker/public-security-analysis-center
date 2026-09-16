<x-modal :id="'payment-proof-' . $occurrence->id" :title="'გადახდის დასტური — საქმე #' . $occurrence->id"
    height="auto">
    <div class="p-4">
        <dl class="row small mb-4">
            <dt class="col-sm-4">ფილიალი</dt>
            <dd class="col-sm-8">{{ $occurrence->branch_name_snapshot }}</dd>
            <dt class="col-sm-4">სერვისი</dt>
            <dd class="col-sm-8">{{ $occurrence->service_name_snapshot }}</dd>
            <dt class="col-sm-4">ბოლო ვადა</dt>
            <dd class="col-sm-8">{{ $occurrence->due_date?->format('d.m.Y') ?? '—' }}</dd>
        </dl>

        @if ($occurrence->payment_proof_path)
            <div class="alert alert-info">
                <div class="fw-semibold mb-1">დოკუმენტი ატვირთულია</div>
                <div class="small mb-2">
                    {{ $occurrence->payment_proof_original_name }}
                    @if ($occurrence->payment_proof_uploaded_at)
                        — {{ $occurrence->payment_proof_uploaded_at->copy()->setTimezone('Asia/Tbilisi')->format('d.m.Y H:i') }}
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('management.task-occurrences.payment-proof.show', $occurrence) }}"
                        target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>ნახვა
                    </a>
                    <a href="{{ route('management.task-occurrences.payment-proof.download', $occurrence) }}"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download me-1"></i>ჩამოტვირთვა
                    </a>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('management.task-occurrences.payment-proof.store', $occurrence) }}"
            enctype="multipart/form-data">
            @csrf
            <label for="payment-proof-file-{{ $occurrence->id }}" class="form-label">
                {{ $occurrence->payment_proof_path ? 'დოკუმენტის შეცვლა' : 'ქვითრის ან ტრანზაქციის სქრინშოტის ატვირთვა' }}
            </label>
            <input id="payment-proof-file-{{ $occurrence->id }}" type="file" name="payment_proof"
                class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
            <div class="form-text">PDF, JPG, PNG ან WEBP — მაქსიმუმ 5MB</div>
            <button type="submit" class="btn btn-primary mt-3">
                <i class="bi bi-cloud-upload me-1"></i>ატვირთვა
            </button>
        </form>
    </div>
</x-modal>
