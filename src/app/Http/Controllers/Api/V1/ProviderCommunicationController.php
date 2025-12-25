<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\MarkAsReadRequest;
use Fereydooni\Shopping\app\Http\Requests\ReplyToCommunicationRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchCommunicationRequest;
use Fereydooni\Shopping\app\Http\Requests\SendCommunicationRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProviderCommunicationRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProviderCommunicationRequest;
use Fereydooni\Shopping\app\Http\Resources\ProviderCommunicationCollection;
use Fereydooni\Shopping\app\Http\Resources\ProviderCommunicationResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderCommunicationSearchResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderCommunicationStatisticsResource;
use Fereydooni\Shopping\app\Http\Resources\ProviderCommunicationThreadResource;
use Fereydooni\Shopping\app\Models\ProviderCommunication;
use Fereydooni\Shopping\app\Services\ProviderCommunicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderCommunicationController extends Controller
{
    public function __construct(
        private ProviderCommunicationService $providerCommunicationService
    ) {}

    public function index(Request $request): JsonResource
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $perPage = $request->get('per_page', 15);
        $communications = $this->providerCommunicationService->paginate($perPage);

        return new ProviderCommunicationCollection($communications);
    }

    public function store(StoreProviderCommunicationRequest $request): JsonResponse
    {
        $this->authorize('create', ProviderCommunication::class);

        $data = $request->validated();
        $communication = $this->providerCommunicationService->create($data);

        return response()->json([
            'message' => 'Provider communication created successfully',
            'data' => new ProviderCommunicationResource($communication),
        ], 201);
    }

    public function show(ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('view', $providerCommunication);

        return response()->json([
            'data' => new ProviderCommunicationResource($providerCommunication),
        ]);
    }

    public function update(UpdateProviderCommunicationRequest $request, ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('update', $providerCommunication);

        $data = $request->validated();
        $updated = $this->providerCommunicationService->update($providerCommunication, $data);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed to update provider communication',
            ], 500);
        }

        $communication = $this->providerCommunicationService->find($providerCommunication->id);

        return response()->json([
            'message' => 'Provider communication updated successfully',
            'data' => new ProviderCommunicationResource($communication),
        ]);
    }

    public function destroy(ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('delete', $providerCommunication);

        $deleted = $this->providerCommunicationService->delete($providerCommunication);

        if (! $deleted) {
            return response()->json([
                'message' => 'Failed to delete provider communication',
            ], 500);
        }

        return response()->json([
            'message' => 'Provider communication deleted successfully',
        ]);
    }

    public function send(SendCommunicationRequest $request): JsonResponse
    {
        $this->authorize('send', ProviderCommunication::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['direction'] = 'outbound';
        $data['status'] = 'sent';
        $data['sent_at'] = now();

        $communication = $this->providerCommunicationService->create($data);

        return response()->json([
            'message' => 'Communication sent successfully',
            'data' => new ProviderCommunicationResource($communication),
        ], 201);
    }

    public function reply(ReplyToCommunicationRequest $request, ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('reply', $providerCommunication);

        $data = $request->validated();
        $data['provider_id'] = $providerCommunication->provider_id;
        $data['user_id'] = auth()->id();
        $data['direction'] = 'outbound';
        $data['status'] = 'sent';
        $data['sent_at'] = now();
        $data['parent_id'] = $providerCommunication->id;
        $data['thread_id'] = $providerCommunication->thread_id ?: $providerCommunication->id;

        $reply = $this->providerCommunicationService->create($data);

        // Update parent communication status
        $this->providerCommunicationService->markAsReplied($providerCommunication);

        return response()->json([
            'message' => 'Reply sent successfully',
            'data' => new ProviderCommunicationResource($reply),
        ], 201);
    }

    public function markAsRead(MarkAsReadRequest $request, ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('read', $providerCommunication);

        $data = $request->validated();
        $readAt = $data['read_at'] ?? now();

        $updated = $this->providerCommunicationService->markAsRead($providerCommunication, $readAt);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed to mark communication as read',
            ], 500);
        }

        return response()->json([
            'message' => 'Communication marked as read successfully',
        ]);
    }

    public function markAsReplied(ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('reply', $providerCommunication);

        $updated = $this->providerCommunicationService->markAsReplied($providerCommunication);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed to mark communication as replied',
            ], 500);
        }

        return response()->json([
            'message' => 'Communication marked as replied successfully',
        ]);
    }

    public function archive(ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('archive', $providerCommunication);

        $updated = $this->providerCommunicationService->archive($providerCommunication);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed to archive communication',
            ], 500);
        }

        return response()->json([
            'message' => 'Communication archived successfully',
        ]);
    }

    public function unarchive(ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('archive', $providerCommunication);

        $updated = $this->providerCommunicationService->unarchive($providerCommunication);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed to unarchive communication',
            ], 500);
        }

        return response()->json([
            'message' => 'Communication unarchived successfully',
        ]);
    }

    public function setUrgent(ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('update', $providerCommunication);

        $updated = $this->providerCommunicationService->setUrgent($providerCommunication);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed to set communication as urgent',
            ], 500);
        }

        return response()->json([
            'message' => 'Communication set as urgent successfully',
        ]);
    }

    public function unsetUrgent(ProviderCommunication $providerCommunication): JsonResponse
    {
        $this->authorize('update', $providerCommunication);

        $updated = $this->providerCommunicationService->unsetUrgent($providerCommunication);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed to unset communication urgency',
            ], 500);
        }

        return response()->json([
            'message' => 'Communication urgency unset successfully',
        ]);
    }

    public function search(SearchCommunicationRequest $request): JsonResource
    {
        $this->authorize('search', ProviderCommunication::class);

        $data = $request->validated();
        $results = $this->providerCommunicationService->searchCommunications($data['query'], $data);

        return new ProviderCommunicationSearchResource($results, $data);
    }

    public function thread(string $threadId): JsonResponse
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $thread = $this->providerCommunicationService->findByThreadId($threadId);

        return response()->json([
            'data' => new ProviderCommunicationThreadResource($thread),
        ]);
    }

    public function conversation(Request $request, int $providerId): JsonResponse
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $userId = $request->get('user_id', auth()->id());
        $limit = $request->get('limit', 50);

        $conversation = $this->providerCommunicationService->findConversation($providerId, $userId, $limit);

        return response()->json([
            'data' => ProviderCommunicationResource::collection($conversation),
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $providerId = $request->get('provider_id');

        if ($providerId) {
            $analytics = $this->providerCommunicationService->getCommunicationAnalytics($providerId);
        } else {
            $analytics = $this->providerCommunicationService->getGlobalCommunicationAnalytics();
        }

        return response()->json([
            'data' => new ProviderCommunicationStatisticsResource($analytics),
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $providerId = $request->get('provider_id');

        if ($providerId) {
            $stats = [
                'total_count' => $this->providerCommunicationService->getCommunicationCount($providerId),
                'unread_count' => $this->providerCommunicationService->getUnreadCount($providerId),
                'unreplied_count' => $this->providerCommunicationService->getUnrepliedCount($providerId),
                'urgent_count' => $this->providerCommunicationService->getUrgentCount($providerId),
                'archived_count' => $this->providerCommunicationService->getArchivedCount($providerId),
                'average_response_time' => $this->providerCommunicationService->getAverageResponseTime($providerId),
                'average_satisfaction' => $this->providerCommunicationService->getAverageSatisfactionRating($providerId),
            ];
        } else {
            $stats = [
                'total_count' => $this->providerCommunicationService->getTotalCommunicationCount(),
                'unread_count' => $this->providerCommunicationService->getTotalUnreadCount(),
                'unreplied_count' => $this->providerCommunicationService->getTotalUnrepliedCount(),
                'urgent_count' => $this->providerCommunicationService->getTotalUrgentCount(),
                'archived_count' => $this->providerCommunicationService->getTotalArchivedCount(),
                'average_response_time' => $this->providerCommunicationService->getTotalAverageResponseTime(),
                'average_satisfaction' => $this->providerCommunicationService->getTotalAverageSatisfactionRating(),
            ];
        }

        return response()->json([
            'data' => $stats,
        ]);
    }

    public function byProvider(int $providerId, Request $request): JsonResource
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $perPage = $request->get('per_page', 15);
        $communications = $this->providerCommunicationService->findByProviderId($providerId);

        return new ProviderCommunicationCollection($communications);
    }

    public function byType(string $type, Request $request): JsonResource
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $perPage = $request->get('per_page', 15);
        $communications = $this->providerCommunicationService->findByCommunicationType($type);

        return new ProviderCommunicationCollection($communications);
    }

    public function urgent(Request $request): JsonResource
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $limit = $request->get('limit', 10);
        $providerId = $request->get('provider_id');

        if ($providerId) {
            $communications = $this->providerCommunicationService->getUrgentCommunicationsByProvider($providerId, $limit);
        } else {
            $communications = $this->providerCommunicationService->getUrgentCommunications($limit);
        }

        return new ProviderCommunicationCollection($communications);
    }

    public function unread(Request $request): JsonResource
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $limit = $request->get('limit', 10);
        $providerId = $request->get('provider_id');

        if ($providerId) {
            $communications = $this->providerCommunicationService->findByProviderAndStatus($providerId, 'unread');
        } else {
            $communications = $this->providerCommunicationService->findUnread();
        }

        return new ProviderCommunicationCollection($communications);
    }

    public function unreplied(Request $request): JsonResource
    {
        $this->authorize('viewAny', ProviderCommunication::class);

        $limit = $request->get('limit', 10);
        $providerId = $request->get('provider_id');

        if ($providerId) {
            $communications = $this->providerCommunicationService->getUnrepliedCommunicationsByProvider($providerId, $limit);
        } else {
            $communications = $this->providerCommunicationService->getUnrepliedCommunications($limit);
        }

        return new ProviderCommunicationCollection($communications);
    }
}
