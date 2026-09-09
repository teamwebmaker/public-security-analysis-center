<?php

namespace App\Services\Resources;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BranchResourceFilterService
{
    /**
     * @param  array<string, string>  $searchRelations
     * @param  array<int, string>  $signatureRelations
     */
    public function apply(
        Builder $query,
        Request $request,
        array $searchRelations,
        array $signatureRelations
    ): Builder {
        $search = trim((string) $request->query('search'));

        $query->when($search !== '', function (Builder $query) use ($search, $searchRelations) {
            $like = "%{$search}%";

            $query->where(function (Builder $query) use ($like, $searchRelations) {
                $query->where('title', 'LIKE', $like)
                    ->orWhere('document_original_name', 'LIKE', $like)
                    ->orWhereHas('branch', fn (Builder $branch) => $branch->where('name', 'LIKE', $like))
                    ->orWhereHas(
                        'branch.company',
                        fn (Builder $company) => $company->where('name', 'LIKE', $like)
                    )
                    ->orWhereHas('creator', fn (Builder $creator) => $creator->where('full_name', 'LIKE', $like));

                foreach ($searchRelations as $relation => $column) {
                    $query->orWhereHas(
                        $relation,
                        fn (Builder $related) => $related->where($column, 'LIKE', $like)
                    );
                }
            });
        });

        $companyId = $this->positiveInteger($request->query('company_id'));
        $branchId = $this->positiveInteger($request->query('branch_id'));
        $documentVisibility = $request->query('document_visibility');
        $signatureStatus = $request->query('signature_status');

        $query->when(
            $companyId !== null,
            fn (Builder $query) => $query->whereHas(
                'branch',
                fn (Builder $branch) => $branch->where('company_id', $companyId)
            )
        );
        $query->when($branchId !== null, fn (Builder $query) => $query->where('branch_id', $branchId));
        $query->when(
            in_array($documentVisibility, ['private', 'public'], true),
            fn (Builder $query) => $query->where('document_visibility', $documentVisibility)
        );

        if ($signatureStatus === 'complete') {
            foreach ($signatureRelations as $relation) {
                $query->whereDoesntHave($relation, fn (Builder $participants) => $participants->whereNull('signed_at'));
            }
        } elseif ($signatureStatus === 'pending') {
            $query->where(function (Builder $query) use ($signatureRelations) {
                foreach ($signatureRelations as $index => $relation) {
                    $method = $index === 0 ? 'whereHas' : 'orWhereHas';
                    $query->{$method}(
                        $relation,
                        fn (Builder $participants) => $participants->whereNull('signed_at')
                    );
                }
            });
        }

        return $query;
    }

    /**
     * Build company and branch choices strictly from the authorized base query.
     *
     * @return array{companies: array<int, string>, branches: array<int, string>}
     */
    public function options(Builder $authorizedQuery): array
    {
        $branchIds = (clone $authorizedQuery)
            ->reorder()
            ->select('branch_id')
            ->distinct()
            ->pluck('branch_id');

        $branches = Branch::query()
            ->whereIn('id', $branchIds)
            ->with('company:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'company_id']);

        return [
            'companies' => $branches
                ->pluck('company')
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->pluck('name', 'id')
                ->all(),
            'branches' => $branches->pluck('name', 'id')->all(),
        ];
    }

    private function positiveInteger(mixed $value): ?int
    {
        return ctype_digit((string) $value) && (int) $value > 0 ? (int) $value : null;
    }
}
