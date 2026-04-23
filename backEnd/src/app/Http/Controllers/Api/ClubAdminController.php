<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubAdminResource;
use App\Services\ClubAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ClubAdminController extends Controller
{
    private $clubAdminService;

    public function __construct(ClubAdminService $clubAdminService)
    {
        $this->clubAdminService = $clubAdminService;
    }

    #[
        OA\Post(
            path: '/club-admins',
            summary: 'Create a new club admin',
            description: 'Creates a new club admin with the provided data',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'nomClub',
                            type: 'string',
                            description: 'The name of the club'
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            description: 'The description of the club'
                        ),
                        new OA\Property(
                            property: 'ecole',
                            type: 'string',
                            description: 'The school associated with the club'
                        ),
                        new OA\Property(
                            property: 'tactique',
                            type: 'string',
                            description: 'The tactical approach of the club'
                        ),
                        new OA\Property(
                            property: 'gestion',
                            type: 'string',
                            description: 'The management style of the club'
                        ),
                    ]
                )
            ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: 'Club admin created successfully',
                    content: new OA\JsonContent(ref: '#/components/schemas/ClubAdminResource')
                ),
                new OA\Response(
                    response: 422,
                    description: 'Validation error'
                ),
                new OA\Response(
                    response: 409,
                    description: 'Club admin already exists for this user'
                ),
                new OA\Response(
                    response: 401,
                    description: 'Unauthorized'
                ),
            ],
        ),
    ]
    public function create(Request $request)
    {
        try {
            $data = $request->validate([
                'nomClub' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'ecole' => 'nullable|string|max:255',
                'tactique' => 'nullable|string|max:255',
                'gestion' => 'nullable|string|max:255',
            ]);

            $data['user_id'] = Auth::id();

            $clubAdmin = $this->clubAdminService->createClubAdmin($data);

            return response()->json(new ClubAdminResource($clubAdmin), 201);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    #[
        OA\Get(
            path: '/club-admins/user/{userId}',
            summary: 'Get club admin by user ID',
            description: 'Retrieves a club admin based on the associated user ID',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            parameters: [
                new OA\Parameter(
                    name: 'userId',
                    in: 'path',
                    required: true,
                    description: 'The ID of the user associated with the club admin',
                    schema: new OA\Schema(type: 'integer')
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Club admin retrieved successfully',
                    content: new OA\JsonContent(ref: '#/components/schemas/ClubAdminResource')
                ),
                new OA\Response(
                    response: 404,
                    description: 'Club admin not found'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Forbidden'
                ),
            ]
        ),
    ]
    public function getByUserId($userId)
    {
        if ((int) $userId !== (int) Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $clubAdmin = $this->clubAdminService->getClubAdminByUserId($userId);

        if (!$clubAdmin) {
            return response()->json(['message' => 'ClubAdmin not found'], 404);
        }

        return response()->json(new ClubAdminResource($clubAdmin));
    }

    #[
        OA\Put(
            path: '/club-admins/user',
            summary: 'Update club admin by user ID',
            description: 'Updates a club admin based on the associated user ID',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'nomClub',
                            type: 'string',
                            description: 'The name of the club'
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            description: 'The description of the club'
                        ),
                        new OA\Property(
                            property: 'ecole',
                            type: 'string',
                            description: 'The school associated with the club'
                        ),
                        new OA\Property(
                            property: 'tactique',
                            type: 'string',
                            description: 'The tactical approach of the club'
                        ),
                        new OA\Property(
                            property: 'gestion',
                            type: 'string',
                            description: 'The management style of the club'
                        ),
                    ]
                )
            ),
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Club admin updated successfully',
                    content: new OA\JsonContent(ref: '#/components/schemas/ClubAdminResource')
                ),
                new OA\Response(
                    response: 404,
                    description: 'Club admin not found'
                ),
                new OA\Response(
                    response: 422,
                    description: 'Validation error'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Forbidden'
                ),
            ],
        ),
    ]
    public function update(Request $request, $userId)
    {
        try {
            if ((int) $userId !== (int) Auth::id()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $data = $request->validate([
                'nomClub' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'ecole' => 'nullable|string|max:255',
                'tactique' => 'nullable|string|max:255',
                'gestion' => 'nullable|string|max:255',
            ]);

            $userId = Auth::id();

            $clubAdmin = $this->clubAdminService->getClubAdminByUserId($userId);
            if (!$clubAdmin) {
                return response()->json(['message' => 'ClubAdmin not found'], 404);
            }

            $updatedClubAdmin = $this->clubAdminService->updateClubAdmin($clubAdmin, $data);

            return response()->json(new ClubAdminResource($updatedClubAdmin));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    #[
        OA\Delete(
            path: '/club-admins/user/{userId}',
            summary: 'Delete club admin by user ID',
            description: 'Deletes a club admin based on the associated user ID',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            parameters: [
                new OA\Parameter(
                    name: 'userId',
                    in: 'path',
                    required: true,
                    description: 'The ID of the user associated with the club admin',
                    schema: new OA\Schema(type: 'integer')
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Club admin deleted successfully'
                ),
                new OA\Response(
                    response: 404,
                    description: 'Club admin not found'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Forbidden'
                ),
            ]
        ),
    ]
    public function delete($userId)
    {
        if ((int) $userId !== (int) Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $clubAdmin = $this->clubAdminService->getClubAdminByUserId($userId);

        if (!$clubAdmin) {
            return response()->json(['message' => 'ClubAdmin not found'], 404);
        }

        $this->clubAdminService->deleteClubAdmin($clubAdmin);

        return response()->json(['message' => 'ClubAdmin deleted successfully']);
    }

    #[
        OA\Get(
            path: '/club-admins',
            summary: 'Get all club admins',
            description: 'Retrieves a list of all club admins',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'List of club admins',
                    content: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: '#/components/schemas/ClubAdminResource')
                    )
                ),
            ]
        ),
    ]
    public function index()
    {
        $clubAdmins = $this->clubAdminService->AllClubAdmins();

        return response()->json(ClubAdminResource::collection($clubAdmins));
    }

    #[
        OA\Get(
            path: '/club-admins/search/{name}',
            summary: 'Search club admins by name',
            description: 'Searches for club admins by their name',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            parameters: [
                new OA\Parameter(
                    name: 'name',
                    in: 'path',
                    required: true,
                    description: 'The name to search for',
                    schema: new OA\Schema(type: 'string')
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Club admins found',
                    content: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: '#/components/schemas/ClubAdminResource')
                    )
                ),
                new OA\Response(
                    response: 404,
                    description: 'No club admins found'
                ),
            ]
        ),
    ]
    public function searchByName($name)
    {
        $clubAdmins = $this->clubAdminService->searchClubAdminsByName($name);

        return response()->json(ClubAdminResource::collection($clubAdmins));
    }

    #[
        OA\Get(
            path: '/club-admins/{id}',
            summary: 'Get club admin by ID',
            description: 'Retrieves a club admin by its ID with all related data including titres and joueurs',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'The ID of the club admin',
                    schema: new OA\Schema(type: 'integer')
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Club admin retrieved successfully',
                    content: new OA\JsonContent(ref: '#/components/schemas/ClubAdminResource')
                ),
                new OA\Response(
                    response: 404,
                    description: 'Club admin not found'
                ),
            ]
        ),
    ]
    public function show($id)
    {
        $clubAdmin = $this->clubAdminService->getClubAdminById($id);

        if (!$clubAdmin) {
            return response()->json(['message' => 'ClubAdmin   SDSfound'], 404);
        }

        return response()->json(new ClubAdminResource($clubAdmin));
    }
    #[
        OA\Get(
            path: '/club-admins/exists/{userId}',
            summary: 'Check if club admin exists for a user',
            description: 'Checks if a club admin exists for the given user ID',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            parameters: [
                new OA\Parameter(
                    name: 'userId',
                    in: 'path',
                    required: true,
                    description: 'The ID of the user to check for club admin existence',
                    schema: new OA\Schema(type: 'integer')
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Club admin existence check result',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(
                                property: 'exists',
                                type: 'boolean',
                                description: 'Indicates whether a club admin exists for the given user ID'
                            ),
                        ]
                    )
                ),
            ]
        ),
    ]
    public function clubAdminExists($userId)
    {
        return response()->json(['exists' => $this->clubAdminService->clubAdminExists($userId)]);
    }

    #[
        OA\Post(
            path: '/club-admins/invite-player',
            summary: 'Create invitation request for a player',
            description: 'Creates a club invitation request for the given player',
            tags: ['ClubAdmin'],
            security: [['bearerAuth' => []]],
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    required: ['joueur_id'],
                    properties: [
                        new OA\Property(
                            property: 'joueur_id',
                            type: 'integer',
                            description: 'The target player ID'
                        ),
                    ]
                )
            ),
            responses: [
                new OA\Response(response: 201, description: 'Invitation request created'),
                new OA\Response(response: 409, description: 'Invitation request conflict'),
                new OA\Response(response: 422, description: 'Validation error'),
            ]
        ),
    ]
    public function invitePlayer(Request $request)
    {
        $data = $request->validate([
            'joueur_id' => 'required|integer|exists:joueurs,id',
        ]);

        try {
            $invitation = $this->clubAdminService->inviteJoueurToClub(Auth::id(), $data['joueur_id']);

            return response()->json([
                'message' => 'Invitation request created successfully.',
                'request' => $invitation,
            ], 201);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }
}
