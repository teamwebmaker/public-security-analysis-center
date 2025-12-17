<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskOccurrenceRequest;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Traits\HandlesFileUpload;

class TaskOccurrenceController extends Controller
{

   use HandlesFileUpload;

   protected array $taskDocument = [
      'field' => 'document',
      'path' => 'documents/tasks/'
   ];
   /**
    * Update an existing task occurrence.
    * Guards: no due_date/start_date/end_date changes (request prohibits).
    * Warns/blocks if latest occurrence is deleted (handled in destroy).
    */
   public function update(UpdateTaskOccurrenceRequest $request, TaskOccurrence $taskOccurrence)
   {
      DB::transaction(function () use ($request, $taskOccurrence) {
         $data = $request->validated();

         // Keep task visibility authoritative: if parent is hidden, force hidden.
         if ($taskOccurrence->task && $taskOccurrence->task->visibility === '0') {
            $data['visibility'] = '0';
         }
         if (array_key_exists('visibility', $data)) {
            $data['visibility'] = $data['visibility'] ? '1' : '0';
         }

         // Handle document upload or deletion
         if ($request->hasFile('document')) {
            $fileName = $this->handleFileUpload(
               $request,
               $this->taskDocument['field'],
               $this->taskDocument['path'],
               $taskOccurrence->document_path ?? ''
            );
            $data['document_path'] = $fileName;
         } elseif ($request->boolean('delete_document')) {
            $this->deleteDocument($taskOccurrence);
            $data['document_path'] = null;
         }

         $taskOccurrence->update($data);
      });

      return back()->with('success', 'ციკლი განახლდა წარმატებით.');
   }

   /**
    * Show edit form for a task occurrence.
    */
   public function edit(TaskOccurrence $taskOccurrence)
   {
      $taskOccurrence->load('task', 'status');
      $statuses = TaskOccurrenceStatus::orderBy('id')->pluck('display_name', 'id')->toArray();

      return view('admin.task_occurrences.edit', compact('taskOccurrence', 'statuses'));
   }

   /**
    * Alias show -> edit to avoid missing method errors.
    */
   public function show(TaskOccurrence $taskOccurrence)
   {
      return redirect()->route('task-occurrences.edit', $taskOccurrence);
   }

   /**
    * Delete an occurrence (not allowed if it is the latest one).
    */
   public function destroy(TaskOccurrence $taskOccurrence)
   {
      if ($taskOccurrence->isLatest()) {
         return back()->with('error', 'ბოლო ციკლის წაშლა შეუძლებელია. წაშალეთ სამუშაო თუ გსურთ ბოლო ციკლის წაშლა.');
      }

      $this->deleteDocument($taskOccurrence);
      $taskOccurrence->delete();

      return back()->with('success', 'ციკლი წაიშალა წარმატებით.');
   }

   /**
    * Delete the occurrence document file if present.
    */
   protected function deleteDocument(TaskOccurrence $taskOccurrence): void
   {
      if (!$taskOccurrence->document_path) {
         return;
      }

      $path = public_path($this->taskDocument['path'] . ltrim($taskOccurrence->document_path, '/'));
      if (File::exists($path)) {
         File::delete($path);
      }

      $taskOccurrence->document_path = null;
      $taskOccurrence->save();
   }
}
