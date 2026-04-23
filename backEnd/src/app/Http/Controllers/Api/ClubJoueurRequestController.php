<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubJoueurRequestResource;
use App\Services\ClubJoueurRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ClubJoueurRequestController extends Controller
{
    public function __construct(private ClubJoueurRequestService $clubJoueurRequestService)
    {
    }

    #[OA\Get(
        path: '/club-joueur-requests',
        summary: 'Get request history for the authenticated user',
        tags: ['ClubJoueurRequest'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'scope',
                in: 'query',
                required: false,
                description: 'Optional scope filter: player or club-admin',
                schema: new OA\Schema(type: 'string', enum: ['player', 'club-admin'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Request history retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/ClubJoueurRequestResource')
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $user = Auth::user();
        $scope = $request->query('scope');

        if ($scope === 'player' && $user?->joueur) {
            return response()->json(ClubJoueurRequestResource::collection(
                $this->clubJoueurRequestService->getPlayerHistory($user->joueur->id)
            ));
        }

        if ($scope === 'club-admin' && $user?->clubAdmin) {
            return response()->json(ClubJoueurRequestResource::collection(
                $this->clubJoueurRequestService->getClubAdminHistory($user->clubAdmin->id)
            ));
        }

        if ($user?->clubAdmin) {
            return response()->json(ClubJoueurRequestResource::collection(
                $this->clubJoueurRequestService->getClubAdminHistory($user->clubAdmin->id)
            ));
        }

        if ($user?->joueur) {
            return response()->json(ClubJoueurRequestResource::collection(
                $this->clubJoueurRequestService->getPlayerHistory($user->joueur->id)
            ));
        }

        return response()->json([]);
    }

    public function myClub()
    {
        $user = Auth::user();

        if (!$user?->joueur) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $currentClub = $this->clubJoueurRequestService->getCurrentClub($user->joueur->id);

        if (!$currentClub) {
            return response()->json(['message' => 'No active club found.'], 404);
        }

        return response()->json(new ClubJoueurRequestResource($currentClub));
    }

    #[OA\Post(
        path: '/club-joueur-requests/{id}/accept',
        summary: 'Accept a club invitation request',
        tags: ['ClubJoueurRequest'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Request accepted successfully'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Request not found'),
        ]
    )]
    public function accept(Request $request, int $id)
    {
        $user = Auth::user();

        if (!$user?->joueur) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $validated = $request->validate([
                'experience_id' => 'nullable|integer|exists:experiences,id',
            ]);

            $updatedRequest = $this->clubJoueurRequestService->acceptRequest(
                $id,
                $user->joueur->id,
                $validated['experience_id'] ?? null,
            );

            return response()->json(new ClubJoueurRequestResource($updatedRequest));
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 409);
            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    #[OA\Post(
        path: '/club-joueur-requests/{id}/reject',
        summary: 'Reject a club invitation request',
        tags: ['ClubJoueurRequest'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Request rejected successfully'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Request not found'),
        ]
    )]
    public function reject(int $id)
    {
        $user = Auth::user();

        if (!$user?->joueur) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $request = $this->clubJoueurRequestService->rejectRequest($id, $user->joueur->id);
            return response()->json(new ClubJoueurRequestResource($request));
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 409);
            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    public function leave()
    {
        $user = Auth::user();

        if (!$user?->joueur) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $request = $this->clubJoueurRequestService->leaveCurrentClub($user->joueur->id);
            return response()->json([
                'message' => 'You have left the club successfully.',
                'request' => new ClubJoueurRequestResource($request),
            ]);
        } catch (\DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }
}
