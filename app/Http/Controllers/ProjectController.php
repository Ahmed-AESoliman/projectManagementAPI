<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'require',
            'description' => 'nullable|min:100'
        ]);
        $authUser = auth()->user();
        $parentId = $authUser->parent_id;
        $data['project_owner'] = $parentId ?? $authUser->id;
        $data['creator_id'] = $authUser->id;
        $project = Project::create($data);
        if ($request->hasFile('attachments')) {
            $this->handlingAttachmentsStore($request, $project);
        }
    }

    private function handlingAttachmentsStore(Request $request, $project)
    {
        $attachments = $project->attachments()->get();

        if ($attachments !== null) {
            if (count($request->attachments_replace)) {
                foreach ($request->attachments_replace as $file) {
                    foreach ($attachments as $attachment) {
                        if ($file == $attachment->file_name) {
                            Storage::disk()->delete($attachment->file_path);
                            $attachment->delete();
                        }
                    }
                }
            }
        }
        foreach ($request->file('attachments') as $attachment) {
            $path = $attachment->storeAs('uploads/projects/' . $project->name . '-' . $project->id, $attachment->hashName());
            $project->attachments()->updateOrCreate([
                'file_path' => $path,
                'file_name' => $project->name . '-' . $attachment->hashName()
            ]);
        }
    }
}
