<?php

namespace App\Services\Instructions;

use Illuminate\Http\Request;

class InstructionFilterService
{
    public function apply($query, Request $request, bool $includeAdminFilters = false)
    {
        $search = trim((string) $request->query('search'));
        $documentVisibility = $request->query('document_visibility');
        $documentType = $request->query('document_type');

        $query->when($search !== '', function ($query) use ($search, $includeAdminFilters) {
            $query->where(function ($query) use ($search, $includeAdminFilters) {
                $like = "%{$search}%";
                $query->where('instructions.name', 'LIKE', $like)
                    ->orWhere('instructions.link', 'LIKE', $like);

                if ($includeAdminFilters) {
                    $query->orWhereHas(
                        'users',
                        fn ($users) => $users->where('full_name', 'LIKE', $like)
                    );
                }
            });
        });

        $query->when(
            in_array($documentVisibility, ['private', 'public'], true),
            fn ($query) => $query->where('instructions.document_visibility', $documentVisibility)
        );

        $query->when(
            in_array($documentType, ['pdf', 'word', 'excel'], true),
            fn ($query) => $this->applyDocumentType($query, $documentType)
        );

        if ($includeAdminFilters) {
            $visibility = $request->query('visibility');
            $workerId = $request->query('worker_id');

            $query->when(
                in_array((string) $visibility, ['0', '1'], true),
                fn ($query) => $query->where('instructions.visibility', (string) $visibility)
            );
            $query->when(
                ctype_digit((string) $workerId) && (int) $workerId > 0,
                fn ($query) => $query->whereHas(
                    'users',
                    fn ($users) => $users->where('users.id', (int) $workerId)
                )
            );
        }

        return $query;
    }

    private function applyDocumentType($query, string $documentType): void
    {
        $extensions = match ($documentType) {
            'pdf' => ['pdf'],
            'word' => ['doc', 'docx'],
            'excel' => ['xls', 'xlsx'],
        };

        $query->where(function ($query) use ($extensions) {
            foreach ($extensions as $extension) {
                $query->orWhereRaw(
                    'LOWER(COALESCE(instructions.document_original_name, instructions.document)) LIKE ?',
                    ["%.{$extension}"]
                );
            }
        });
    }
}
