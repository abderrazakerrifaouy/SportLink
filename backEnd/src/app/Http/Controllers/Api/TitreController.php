<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TitreResource;
use App\Services\TitreService;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class TitreController extends Controller
{
    private $titreService;

    public function __construct(TitreService $titreService)
    {
        $this->titreService = $titreService;
    }

    #[OA\Get(
        path: "/titres",
        summary: "Get all titres",
        tags: ["Titre"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/TitreResource")
                )
            )
        ]
    )]
    public function index()
    {
        $titres = $this->titreService->all();
        return TitreResource::collection($titres);
    }

    #[OA\Get(
        path: "/titres/{id}",
        summary: "Get a titre by ID",
        tags: ["Titre"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the titre to retrieve",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(ref: "#/components/schemas/TitreResource")
            ),
            new OA\Response(
                response: 404,
                description: "Titre not found")
        ]
    )]
    public function show($id)
    {
        $titre = $this->titreService->find($id);
        if (!$titre) {
            return response()->json(['message' => 'Titre not found'], 404);
        }
        return new TitreResource($titre);
    }

    #[OA\Get(
        path: "/club-admins/{id}/titres",
        summary: "Get titres by club admin ID",
        tags: ["Titre"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the club admin to retrieve titres for",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/TitreResource")
                )
            ),
            new OA\Response(
                response: 404,
                description: "Club admin not found")
        ]
    )]
    public function getTitresByClubAdminId($id)
    {
        $titres = $this->titreService->getByClubAdminId($id);
        if (!$titres) {
            return response()->json(['message' => 'Club admin not found'], 404);
        }
        return TitreResource::collection($titres);
    }

    #[OA\Post(
        path: "/titres",
        summary: "Create a new titre",
        tags: ["Titre"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nomTitre", type: "string", description: "Name of the titre"),
                    new OA\Property(property: "description", type: "string", description: "Description of the titre"),
                    new OA\Property(property: "number", type: "integer", description: "Number of the titre"),
                    new OA\Property(property: "club_admin_id", type: "integer", description: "ID of the club admin")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Titre created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/TitreResource")
            )
        ]
    )]

    public function store(Request $request)
    {
        $validetedData = $request->validate([
            'nomTitre' => 'required|string|max:255',
            'description' => 'required|string',
            'number' => 'required|integer'
        ]);
        $validetedData['club_admin_id'] = Auth::user()->clubAdmin->id;
        $titre = $this->titreService->create($validetedData);
        return new TitreResource($titre);
    }

    #[OA\Delete(
        path: "/titres/{id}",
        summary: "Delete a titre",
        tags: ["Titre"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the titre to delete",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Titre deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Titre not found"
            )
        ]
    )]
    public function destroy($id)
    {
        $deleted = $this->titreService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Titre not found'], 404);
        }
        return response()->json(['message' => 'Titre deleted successfully']);
    }

    #[OA\Put(
        path: "/titres/{id}",
        summary: "Update a titre",
        tags: ["Titre"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the titre to update",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nomTitre", type: "string", description: "Name of the titre"),
                    new OA\Property(property: "description", type: "string", description: "Description of the titre"),
                    new OA\Property(property: "number", type: "integer", description: "Number of the titre"),
                    new OA\Property(property: "club_admin_id", type: "integer", description: "ID of the club admin")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Titre updated successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/TitreResource")
            ),
            new OA\Response(
                response: 404,
                description: "Titre not found"
            )
        ]
    )]

    public function update(Request $request, $id)
    {
        $validetedData = $request->validate([
            'nomTitre' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'number' => 'sometimes|required|integer',
        ]);
        $validetedData['club_admin_id'] = Auth::user()->clubAdmin->id;
        $titre = $this->titreService->update($id, $validetedData);
        if (!$titre) {
            return response()->json(['message' => 'Titre not found'], 404);
        }
        return new TitreResource($titre);
    }
}
