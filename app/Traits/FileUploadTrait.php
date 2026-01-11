<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;

trait FileUploadTrait
{

    public function uploadDocuments($files, $filePath, $id, $type)
    {
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $fileName = $this->convertFileName($originalName);
            $path = $filePath['default'];

            // if (!is_dir($path)) {
            //     mkdir($path, 0777, true);
            // }

            if(!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $upload_success = $file->move($path, $fileName);

            if ($upload_success) {
                $data = [
                    'entity_id'     => $id,
                    'entity_type'   => $type,
                    'name'          => $fileName,
                    'orignal_name'  => $originalName,
                    'created_by'    => Auth::user()->id,
                    'updated_by'    => Auth::user()->id
                ];
                Document::create($data);
            }
        }
    }

    private function convertFileName($originalName)
    {
        $newName = time() . str_replace(' ', '-', $originalName);
        return $newName;
    }

}
