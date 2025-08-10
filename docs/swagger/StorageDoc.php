<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class StorageDoc
{
    /**
     * @OA\Get(
     *     path="/api/storage/{path}",
     *     summary="Serve Uploaded Files",
     *     description="Serves uploaded files (images, documents, etc.) via the API with proper content type headers. This route provides access to files stored in the public storage disk.",
     *     operationId="getStorageFile",
     *     tags={"File Storage"},
     *     @OA\Parameter(
     *         name="path",
     *         in="path",
     *         required=true,
     *         description="File path relative to storage/app/public directory",
     *         @OA\Schema(
     *             type="string",
     *             example="products/image.png"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File served successfully",
     *         @OA\MediaType(
     *             mediaType="image/png",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary",
     *                 description="Image file"
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="image/jpeg",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary",
     *                 description="Image file"
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/octet-stream",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary",
     *                 description="Generic file"
     *             )
     *         ),
     *         @OA\Header(
     *             header="Content-Type",
     *             description="MIME type of the file",
     *             @OA\Schema(type="string", example="image/png")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="File not found")
     *         )
     *     )
     * )
     */
    public function getStorageFile()
    {
        // This method exists only for documentation purposes
    }
}
