<?php

namespace App\Services\Firebase;

use Google\Cloud\Firestore\FirestoreClient;

class FirestoreService
{
    /**
     * Connect to firestore
     * 
     * @return FirestoreClient
     */
    public static function connect()
    {
        return new FirestoreClient([
            'projectId' => json_decode(\File::get(base_path(env('FIREBASE_CREDENTIALS'))), true)['project_id']
        ]);
    }
}
