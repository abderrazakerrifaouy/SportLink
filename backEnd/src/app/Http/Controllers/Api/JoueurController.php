<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use Illuminate\Http\Request;
use App\Services\JoueurService;
use App\Http\Resources\JoueurResource;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class JoueurController extends Controller
{
    private $joueurService;

    public function __construct(JoueurService $joueurService)
    {
        $this->joueurService = $joueurService;
    }
    #[OA\Get(
        path: "/joueurs",
        summary: "Get all joueurs",
        tags: ["Joueur"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/JoueurResource")
                )
            )
        ]
    )]
    public function index()
    {
        $joueurs = $this->joueurService->getAllJoueurs();
        return JoueurResource::collection($joueurs);
    }

    #[OA\Get(
        path: "/joueurs/{id}",
        summary: "Get a specific joueur",
        tags: ["Joueur"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the joueur",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(ref: "#/components/schemas/JoueurResource")
            ),
            new OA\Response(
                response: 404,
                description: "Joueur not found"
            )
        ]
    )]
    public function show($id)
    {
        $joueur = $this->joueurService->getJoueurById($id);
        if ($joueur) {
            return new JoueurResource($joueur);
        }
        return response()->json(['message' => 'Joueur not found'], 404);
    }

    #[OA\Post(
        path: "/joueurs",
        summary: "Create a new joueur",
        tags: ["Joueur"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Joueur created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/JoueurResource")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid input data"
            )
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $joueur = $this->joueurService->createJoueur($data);
        return new JoueurResource($joueur);
    }

    #[OA\Get(
        path: "/joueurs/{id}/experiences",
        summary: "Get experiences for a specific joueur",
        tags: ["Joueur"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the joueur",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/ExperienceResource")
                )
            )
        ]
    )]
    public function getExperiences($id)
    {
        $experiences = $this->joueurService->getExperiencesByJoueurId($id);
        return response()->json($experiences);
    }

    #[OA\Post(
        path: "/experiences",
        summary: "Create a new experience for a specific joueur",
        tags: ["Joueur"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nomClub", "endDate"],
                properties: [
                    new OA\Property(property: "nomClub", type: "string", example: "Club de football"),
                    new OA\Property(property: "joinDate", type: "string", format: "date", example: "2020-01-01"),
                    new OA\Property(property: "endDate", type: "string", format: "date", example: "2021-01-01"),
                    new OA\Property(property: "place", type: "string", example: "Paris"),
                    new OA\Property(property: "categoryType", type: "string", enum: ["SENIOR", "ESPOIR", "JUNIOR", "CADET", "MINIM"], example: "SENIOR")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Experience created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/ExperienceResource")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid input data"
            )
        ]
    )]
    public function createExperience(Request $request )
    {
        $data = $request->validate([
            'nomClub' => 'required|string|max:255',
            'joinDate' => 'nullable|date',
            'endDate' => 'required|date|after_or_equal:joinDate',
            'place' => 'nullable|string|max:255',
            'categoryType' => 'nullable|in:SENIOR,ESPOIR,JUNIOR,CADET,MINIM',
        ]);
        $joueurId = $request->user()->joueur->id;
        $data['joueur_id'] = $joueurId;

        $experience = $this->joueurService->createExperience($data);
        return response()->json(new ExperienceResource($experience), 201);
    }

    #[OA\Delete(
        path: "/joueurs/{id}/experiences/{experienceId}",
        summary: "Delete a specific experience for a joueur",
        tags: ["Joueur"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the joueur",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "experienceId",
                in: "path",
                description: "ID of the experience",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Experience deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Experience not found"
            )
        ]
    )]

    public function deleteExperience($experienceId)
    {
        $deleted = $this->joueurService->deleteExperience($experienceId);
        if ($deleted) {
            return response()->json(['message' => 'Experience deleted successfully']);
        }
        return response()->json(['message' => 'Experience not found'], 404);
    }

    #[OA\Put(
        path: "/joueurs/{id}/experiences/{experienceId}",
        summary: "Update a specific experience for a joueur",
        tags: ["Joueur"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the joueur",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "experienceId",
                in: "path",
                description: "ID of the experience",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nomClub", type: "string", example: "Club de football"),
                    new OA\Property(property: "joinDate", type: "string", format: "date", example: "2020-01-01"),
                    new OA\Property(property: "endDate", type: "string", format: "date", example: "2021-01-01"),
                    new OA\Property(property: "place", type: "string", example: "Paris"),
                    new OA\Property(property: "categoryType", type: "string", enum: ["SENIOR", "ESPOIR", "JUNIOR", "CADET", "MINIM"], example: "SENIOR")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Experience updated successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/ExperienceResource")
            ),
            new OA\Response(
                response: 404,
                description: "Experience not found"
            )
        ]
    )]
    public function updateExperience(Request $request, $experienceId)
    {
        $data = $request->validate([
            'nomClub' => 'nullable|string|max:255',
            'joinDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:joinDate',
            'place' => 'nullable|string|max:255',
            'categoryType' => 'nullable|in:SENIOR,ESPOIR,JUNIOR,CADET,MINIM',
        ]);

        $experience = $this->joueurService->updateExperience($experienceId, $data);
        if ($experience) {
            return response()->json(new ExperienceResource($experience));
        }
        return response()->json(['message' => 'Experience not found'], 404);
    }

}
