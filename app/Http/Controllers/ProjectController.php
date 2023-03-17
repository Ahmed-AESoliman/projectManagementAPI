<?php

namespace App\Http\Controllers;

use App\Models\attachment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProjectController extends Controller
{
    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        try {
            $authUser = auth()->user();

            $data['company_id'] = $authUser->company_id;
            $data['creator_id'] = $authUser->id;
            $project = Project::create($data);
            if ($request->hasFile('attachments')) {
                $this->handlingAttachmentsStore($request, $project);
            }
            return response()->json(['message' => 'project was created Successfully!'], 200);
        } catch (Exception  $Exception) {
            throw new Exception('something went wrong and project couldn\'t be created');
        }
    }
    public function update(Request $request, $id)
    {
        $project = Project::find($id);
        if ($project) {
            $data = $request->validate([
                'name' => 'required',
                'description' => 'required'
            ]);
            try {
                $project = $project->update($data);
                return response()->json(['message' => 'project was updated Successfully!'], 200);
            } catch (Exception  $Exception) {
                throw new Exception('something went wrong and project couldn\'t be updated');
            }
        }
        return response()->json(['message' => 'project not found'], 404);
    }
    public function removeProjectAttachment(Request $request)
    {
        $data = $request->validate([
            'path' => 'required',
        ]);
        $attachment = attachment::where('file_path', $data['path']);
        if ($attachment) {
            try {
                Storage::disk()->delete($attachment->file_path);
                $attachment->delete();
                return response()->json(['message' => 'attachment was removed Successfully!'], 200);
            } catch (Exception  $Exception) {
                throw new Exception('something went wrong and attachment couldn\'t be removed');
            }
        }
    }
    private function handlingAttachmentsStore(Request $request, $project)
    {
        try {
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->storeAs('uploads/projects/' . $project->name . '-' . $project->id, $attachment->hashName());
                $project->attachments()->updateOrCreate([
                    'file_path' => $path,
                    'file_name' => $project->name . '-' . $attachment->hashName()
                ]);
            }
            return true;
        } catch (Exception  $th) {
            throw new Exception('something went wrong and attachment couldn\'t be added');
        }
    }
}
