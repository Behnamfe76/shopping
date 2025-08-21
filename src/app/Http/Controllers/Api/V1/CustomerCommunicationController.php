<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Fereydooni\Shopping\app\Services\CustomerCommunicationService;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Fereydooni\Shopping\app\DTOs\CustomerCommunicationDTO;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerCommunicationController extends Controller
{
    protected CustomerCommunicationService $service;

    public function __construct(CustomerCommunicationService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of customer communications.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CustomerCommunication::class);

        $perPage = $request->get('per_page', 15);
        $communications = $this->service->paginate($perPage);

        return response()->json([
            'data' => $communications->items(),
            'meta' => [
                'current_page' => $communications->currentPage(),
                'last_page' => $communications->lastPage(),
                'per_page' => $communications->perPage(),
                'total' => $communications->total(),
            ],
        ]);
    }

    /**
     * Store a newly created customer communication.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', CustomerCommunication::class);

        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'user_id' => 'required|integer|exists:users,id',
            'communication_type' => 'required|string|in:email,sms,push_notification,in_app,letter,phone_call',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'sometimes|string|in:draft,scheduled,sending,sent,delivered,opened,clicked,bounced,unsubscribed,cancelled,failed',
            'priority' => 'sometimes|string|in:low,normal,high,urgent',
            'channel' => 'sometimes|string|in:email,sms,push,web,mobile,mail,phone',
            'scheduled_at' => 'sometimes|date|after:now',
            'campaign_id' => 'sometimes|integer',
            'segment_id' => 'sometimes|integer',
            'template_id' => 'sometimes|integer',
            'metadata' => 'sometimes|array',
            'tracking_data' => 'sometimes|array',
        ]);

        $communication = $this->service->createCommunication($validated);

        return response()->json([
            'message' => 'Customer communication created successfully',
            'data' => $communication,
        ], 201);
    }

    /**
     * Display the specified customer communication.
     */
    public function show(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('view', $communication);

        return response()->json([
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Update the specified customer communication.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('update', $communication);

        $validated = $request->validate([
            'customer_id' => 'sometimes|integer|exists:customers,id',
            'user_id' => 'sometimes|integer|exists:users,id',
            'communication_type' => 'sometimes|string|in:email,sms,push_notification,in_app,letter,phone_call',
            'subject' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'status' => 'sometimes|string|in:draft,scheduled,sending,sent,delivered,opened,clicked,bounced,unsubscribed,cancelled,failed',
            'priority' => 'sometimes|string|in:low,normal,high,urgent',
            'channel' => 'sometimes|string|in:email,sms,push,web,mobile,mail,phone',
            'scheduled_at' => 'sometimes|date|after:now',
            'campaign_id' => 'sometimes|integer',
            'segment_id' => 'sometimes|integer',
            'template_id' => 'sometimes|integer',
            'metadata' => 'sometimes|array',
            'tracking_data' => 'sometimes|array',
        ]);

        $updatedCommunication = $this->service->updateCommunication($id, $validated);

        if (!$updatedCommunication) {
            return response()->json(['message' => 'Failed to update customer communication'], 400);
        }

        return response()->json([
            'message' => 'Customer communication updated successfully',
            'data' => $updatedCommunication,
        ]);
    }

    /**
     * Remove the specified customer communication.
     */
    public function destroy(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('delete', $communication);

        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete customer communication'], 400);
        }

        return response()->json(['message' => 'Customer communication deleted successfully']);
    }

    /**
     * Schedule a customer communication.
     */
    public function schedule(Request $request, int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('schedule', $communication);

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $scheduled = $this->service->schedule($communication, $validated['scheduled_at']);

        if (!$scheduled) {
            return response()->json(['message' => 'Failed to schedule customer communication'], 400);
        }

        return response()->json([
            'message' => 'Customer communication scheduled successfully',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Send a customer communication.
     */
    public function send(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('send', $communication);

        $sent = $this->service->send($communication);

        if (!$sent) {
            return response()->json(['message' => 'Failed to send customer communication'], 400);
        }

        return response()->json([
            'message' => 'Customer communication sent successfully',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Cancel a customer communication.
     */
    public function cancel(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('cancel', $communication);

        $cancelled = $this->service->cancel($communication);

        if (!$cancelled) {
            return response()->json(['message' => 'Failed to cancel customer communication'], 400);
        }

        return response()->json([
            'message' => 'Customer communication cancelled successfully',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Reschedule a customer communication.
     */
    public function reschedule(Request $request, int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('reschedule', $communication);

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $rescheduled = $this->service->reschedule($communication, $validated['scheduled_at']);

        if (!$rescheduled) {
            return response()->json(['message' => 'Failed to reschedule customer communication'], 400);
        }

        return response()->json([
            'message' => 'Customer communication rescheduled successfully',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Mark a customer communication as delivered.
     */
    public function markAsDelivered(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('markAsDelivered', $communication);

        $marked = $this->service->markAsDelivered($communication);

        if (!$marked) {
            return response()->json(['message' => 'Failed to mark customer communication as delivered'], 400);
        }

        return response()->json([
            'message' => 'Customer communication marked as delivered',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Mark a customer communication as opened.
     */
    public function markAsOpened(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('markAsOpened', $communication);

        $marked = $this->service->markAsOpened($communication);

        if (!$marked) {
            return response()->json(['message' => 'Failed to mark customer communication as opened'], 400);
        }

        return response()->json([
            'message' => 'Customer communication marked as opened',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Mark a customer communication as clicked.
     */
    public function markAsClicked(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('markAsClicked', $communication);

        $marked = $this->service->markAsClicked($communication);

        if (!$marked) {
            return response()->json(['message' => 'Failed to mark customer communication as clicked'], 400);
        }

        return response()->json([
            'message' => 'Customer communication marked as clicked',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Mark a customer communication as bounced.
     */
    public function markAsBounced(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('markAsBounced', $communication);

        $marked = $this->service->markAsBounced($communication);

        if (!$marked) {
            return response()->json(['message' => 'Failed to mark customer communication as bounced'], 400);
        }

        return response()->json([
            'message' => 'Customer communication marked as bounced',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Mark a customer communication as unsubscribed.
     */
    public function markAsUnsubscribed(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('markAsUnsubscribed', $communication);

        $marked = $this->service->markAsUnsubscribed($communication);

        if (!$marked) {
            return response()->json(['message' => 'Failed to mark customer communication as unsubscribed'], 400);
        }

        return response()->json([
            'message' => 'Customer communication marked as unsubscribed',
            'data' => $this->service->findDTO($id),
        ]);
    }

    /**
     * Get communication analytics.
     */
    public function analytics(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('viewAnalytics', CustomerCommunication::class);

        $analytics = $this->service->getCommunicationAnalytics($communication->customer_id);

        return response()->json([
            'data' => $analytics,
        ]);
    }

    /**
     * Get tracking data for a communication.
     */
    public function tracking(int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('viewTrackingData', $communication);

        $trackingData = $this->service->getTrackingData($communication);

        return response()->json([
            'data' => $trackingData,
        ]);
    }

    /**
     * Add attachment to a communication.
     */
    public function addAttachment(Request $request, int $id): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('manageAttachments', $communication);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $added = $this->service->addAttachment($communication, $request->file('file'));

        if (!$added) {
            return response()->json(['message' => 'Failed to add attachment'], 400);
        }

        return response()->json([
            'message' => 'Attachment added successfully',
            'data' => $this->service->getAttachments($communication),
        ]);
    }

    /**
     * Remove attachment from a communication.
     */
    public function removeAttachment(int $id, int $mediaId): JsonResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            return response()->json(['message' => 'Customer communication not found'], 404);
        }

        $this->authorize('manageAttachments', $communication);

        $removed = $this->service->removeAttachment($communication, $mediaId);

        if (!$removed) {
            return response()->json(['message' => 'Failed to remove attachment'], 400);
        }

        return response()->json([
            'message' => 'Attachment removed successfully',
        ]);
    }
}
