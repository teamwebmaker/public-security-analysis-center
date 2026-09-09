<?php

namespace App\Services\DocumentTemplates;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DocumentTemplateFilterService
{
    public function apply($query, Request $request, bool $includeAdminFilters = false)
    {
        $search = trim((string) $request->query('search'));
        $documentType = $request->query('document_type');

        $query->when($search !== '', function (Builder $query) use ($search, $includeAdminFilters) {
            $like = "%{$search}%";

            $query->where(function (Builder $query) use ($like, $includeAdminFilters) {
                $query->where('document_templates.name', 'LIKE', $like)
                    ->orWhere('document_templates.document', 'LIKE', $like);

                if ($includeAdminFilters) {
                    $query->orWhereHas(
                        'users',
                        fn (Builder $users) => $users->where('full_name', 'LIKE', $like)
                    );
                }
            });
        });

        $query->when(
            in_array($documentType, ['pdf', 'word', 'excel'], true),
            fn (Builder $query) => $this->applyDocumentType($query, $documentType)
        );

        if ($includeAdminFilters) {
            $visibility = $request->query('visibility');
            $workerId = $this->positiveInteger($request->query('worker_id'));

            $query->when(
                in_array((string) $visibility, ['0', '1'], true),
                fn (Builder $query) => $query->where('document_templates.visibility', (string) $visibility)
            );
            $query->when(
                $workerId !== null,
                fn (Builder $query) => $query->whereHas(
                    'users',
                    fn (Builder $users) => $users->where('users.id', $workerId)
                )
            );
        }

        return $query;
    }

    /**
     * @return array<int, string>
     */
    public function workerOptions(): array
    {
        return User::query()
            ->whereHas('role', fn (Builder $role) => $role->where('name', 'worker'))
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->all();
    }

    private function applyDocumentType(Builder $query, string $documentType): void
    {
        $extensions = match ($documentType) {
            'pdf' => ['pdf'],
            'word' => ['doc', 'docx'],
            'excel' => ['xls', 'xlsx'],
        };

        $query->where(function (Builder $query) use ($extensions) {
            foreach ($extensions as $extension) {
                $query->orWhereRaw('LOWER(document_templates.document) LIKE ?', ["%.{$extension}"]);
            }
        });
    }

    private function positiveInteger(mixed $value): ?int
    {
        return ctype_digit((string) $value) && (int) $value > 0 ? (int) $value : null;
    }
}
