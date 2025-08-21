<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Fereydooni\Shopping\app\Services\CustomerCommunicationService;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Fereydooni\Shopping\app\DTOs\CustomerCommunicationDTO;

class CustomerCommunicationController extends Controller
{
    protected CustomerCommunicationService $service;

    public function __construct(CustomerCommunicationService $service)
    {
        $this->service = $service;
    }

    /**
     * Display customer communication dashboard.
     */
    public function dashboard(): View
    {
        $this->authorize('viewAny', CustomerCommunication::class);

        $stats = $this->service->getCommunicationStats();
        $recentCommunications = $this->service->getRecentCommunications(10);
        $scheduledCommunications = $this->service->findScheduled();
        $performanceStats = $this->service->getCommunicationPerformanceStats();

        return view('customer-communications.dashboard', compact(
            'stats',
            'recentCommunications',
            'scheduledCommunications',
            'performanceStats'
        ));
    }

    /**
     * Display a listing of customer communications.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CustomerCommunication::class);

        $perPage = $request->get('per_page', 15);
        $communications = $this->service->paginate($perPage);

        return view('customer-communications.index', compact('communications'));
    }

    /**
     * Show the form for creating a new customer communication.
     */
    public function create(): View
    {
        $this->authorize('create', CustomerCommunication::class);

        return view('customer-communications.create');
    }

    /**
     * Store a newly created customer communication.
     */
    public function store(Request $request): RedirectResponse
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

        return redirect()->route('customer-communications.show', $communication->id)
            ->with('success', 'Customer communication created successfully.');
    }

    /**
     * Display the specified customer communication.
     */
    public function show(int $id): View
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('view', $communication);

        $communicationDTO = $this->service->findDTO($id);
        $attachments = $this->service->getAttachments($communication);
        $trackingData = $this->service->getTrackingData($communication);

        return view('customer-communications.show', compact(
            'communication',
            'communicationDTO',
            'attachments',
            'trackingData'
        ));
    }

    /**
     * Show the form for editing the specified customer communication.
     */
    public function edit(int $id): View
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('update', $communication);

        return view('customer-communications.edit', compact('communication'));
    }

    /**
     * Update the specified customer communication.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
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
            return back()->withErrors(['error' => 'Failed to update customer communication']);
        }

        return redirect()->route('customer-communications.show', $id)
            ->with('success', 'Customer communication updated successfully.');
    }

    /**
     * Remove the specified customer communication.
     */
    public function destroy(int $id): RedirectResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('delete', $communication);

        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return back()->withErrors(['error' => 'Failed to delete customer communication']);
        }

        return redirect()->route('customer-communications.index')
            ->with('success', 'Customer communication deleted successfully.');
    }

    /**
     * Show communication scheduling interface.
     */
    public function schedule(int $id): View
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('schedule', $communication);

        return view('customer-communications.schedule', compact('communication'));
    }

    /**
     * Schedule a customer communication.
     */
    public function scheduleCommunication(Request $request, int $id): RedirectResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('schedule', $communication);

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $scheduled = $this->service->schedule($communication, $validated['scheduled_at']);

        if (!$scheduled) {
            return back()->withErrors(['error' => 'Failed to schedule customer communication']);
        }

        return redirect()->route('customer-communications.show', $id)
            ->with('success', 'Customer communication scheduled successfully.');
    }

    /**
     * Send a customer communication.
     */
    public function send(int $id): RedirectResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('send', $communication);

        $sent = $this->service->send($communication);

        if (!$sent) {
            return back()->withErrors(['error' => 'Failed to send customer communication']);
        }

        return redirect()->route('customer-communications.show', $id)
            ->with('success', 'Customer communication sent successfully.');
    }

    /**
     * Cancel a customer communication.
     */
    public function cancel(int $id): RedirectResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('cancel', $communication);

        $cancelled = $this->service->cancel($communication);

        if (!$cancelled) {
            return back()->withErrors(['error' => 'Failed to cancel customer communication']);
        }

        return redirect()->route('customer-communications.show', $id)
            ->with('success', 'Customer communication cancelled successfully.');
    }

    /**
     * Show communication tracking and analytics.
     */
    public function tracking(int $id): View
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('viewTrackingData', $communication);

        $trackingData = $this->service->getTrackingData($communication);
        $analytics = $this->service->getCommunicationAnalytics($communication->customer_id);

        return view('customer-communications.tracking', compact(
            'communication',
            'trackingData',
            'analytics'
        ));
    }

    /**
     * Show communication templates management.
     */
    public function templates(): View
    {
        $this->authorize('viewAny', CustomerCommunication::class);

        return view('customer-communications.templates');
    }

    /**
     * Show communication campaigns management.
     */
    public function campaigns(): View
    {
        $this->authorize('viewAny', CustomerCommunication::class);

        return view('customer-communications.campaigns');
    }

    /**
     * Show communication import/export interface.
     */
    public function importExport(): View
    {
        $this->authorize('viewAny', CustomerCommunication::class);

        return view('customer-communications.import-export');
    }

    /**
     * Show communication performance reporting.
     */
    public function reporting(): View
    {
        $this->authorize('viewAnalytics', CustomerCommunication::class);

        $performanceStats = $this->service->getCommunicationPerformanceStats();
        $engagementStats = $this->service->getCommunicationEngagementStats();
        $growthStats = $this->service->getCommunicationGrowthStats();

        return view('customer-communications.reporting', compact(
            'performanceStats',
            'engagementStats',
            'growthStats'
        ));
    }

    /**
     * Add attachment to a communication.
     */
    public function addAttachment(Request $request, int $id): RedirectResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('manageAttachments', $communication);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $added = $this->service->addAttachment($communication, $request->file('file'));

        if (!$added) {
            return back()->withErrors(['error' => 'Failed to add attachment']);
        }

        return back()->with('success', 'Attachment added successfully.');
    }

    /**
     * Remove attachment from a communication.
     */
    public function removeAttachment(int $id, int $mediaId): RedirectResponse
    {
        $communication = $this->service->find($id);
        
        if (!$communication) {
            abort(404, 'Customer communication not found');
        }

        $this->authorize('manageAttachments', $communication);

        $removed = $this->service->removeAttachment($communication, $mediaId);

        if (!$removed) {
            return back()->withErrors(['error' => 'Failed to remove attachment']);
        }

        return back()->with('success', 'Attachment removed successfully.');
    }
}
