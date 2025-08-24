<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreProviderCommunicationRequest;
use App\Http\Requests\UpdateProviderCommunicationRequest;
use App\Http\Requests\SendCommunicationRequest;
use App\Http\Requests\ReplyToCommunicationRequest;
use App\Http\Requests\MarkAsReadRequest;
use App\Http\Requests\SearchCommunicationRequest;
use App\Services\ProviderCommunicationService;
use App\Models\ProviderCommunication;
use App\DTOs\ProviderCommunicationDTO;
use Exception;

class ProviderCommunicationController extends Controller
{
    protected $communicationService;

    public function __construct(ProviderCommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\ProviderCommunication');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status');
            $priority = $request->get('priority');
            $type = $request->get('type');
            $direction = $request->get('direction');

            $communications = $this->communicationService->paginateCommunications($perPage);

            // Apply filters if provided
            if ($search) {
                $communications = $this->communicationService->searchCommunications($search);
            }

            return view('provider-communications.index', compact('communications', 'search', 'status', 'priority', 'type', 'direction'));
        } catch (Exception $e) {
            Log::error('Failed to display provider communications index', ['error' => $e->getMessage()]);
            return view('provider-communications.index')->with('error', 'Failed to load communications.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', ProviderCommunication::class);

        return view('provider-communications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProviderCommunicationRequest $request): RedirectResponse
    {
        try {
            $this->authorize('create', ProviderCommunication::class);

            $data = $request->validated();
            $data['user_id'] = Auth::id();

            $communication = $this->communicationService->createCommunication($data);

            Log::info('Provider communication created', [
                'id' => $communication->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('provider-communications.show', $communication)
                ->with('success', 'Communication created successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create provider communication', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withInput()->with('error', 'Failed to create communication.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProviderCommunication $providerCommunication): View
    {
        $this->authorize('view', $providerCommunication);

        try {
            // Mark as read if user is viewing it
            if (Auth::id() === $providerCommunication->user_id) {
                $this->communicationService->markAsRead($providerCommunication);
            }

            // Get thread if this is part of a conversation
            $thread = null;
            if ($providerCommunication->thread_id) {
                $thread = $this->communicationService->getThread($providerCommunication->thread_id);
            }

            return view('provider-communications.show', compact('providerCommunication', 'thread'));
        } catch (Exception $e) {
            Log::error('Failed to display provider communication', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to load communication.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProviderCommunication $providerCommunication): View
    {
        $this->authorize('update', $providerCommunication);

        return view('provider-communications.edit', compact('providerCommunication'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProviderCommunicationRequest $request, ProviderCommunication $providerCommunication): RedirectResponse
    {
        try {
            $this->authorize('update', $providerCommunication);

            $data = $request->validated();
            $result = $this->communicationService->updateCommunication($providerCommunication, $data);

            if ($result) {
                Log::info('Provider communication updated', [
                    'id' => $providerCommunication->id,
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('provider-communications.show', $providerCommunication)
                    ->with('success', 'Communication updated successfully.');
            }

            return back()->withInput()->with('error', 'Failed to update communication.');
        } catch (Exception $e) {
            Log::error('Failed to update provider communication', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error', 'Failed to update communication.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProviderCommunication $providerCommunication): RedirectResponse
    {
        try {
            $this->authorize('delete', $providerCommunication);

            $result = $this->communicationService->deleteCommunication($providerCommunication);

            if ($result) {
                Log::info('Provider communication deleted', [
                    'id' => $providerCommunication->id,
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('provider-communications.index')
                    ->with('success', 'Communication deleted successfully.');
            }

            return back()->with('error', 'Failed to delete communication.');
        } catch (Exception $e) {
            Log::error('Failed to delete provider communication', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete communication.');
        }
    }

    /**
     * Send a new communication
     */
    public function send(SendCommunicationRequest $request): RedirectResponse
    {
        try {
            $this->authorize('create', ProviderCommunication::class);

            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['direction'] = 'outbound';
            $data['status'] = 'sent';

            $communication = $this->communicationService->createCommunication($data);

            Log::info('Communication sent', [
                'id' => $communication->id,
                'provider_id' => $communication->provider_id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('provider-communications.show', $communication)
                ->with('success', 'Communication sent successfully.');
        } catch (Exception $e) {
            Log::error('Failed to send communication', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withInput()->with('error', 'Failed to send communication.');
        }
    }

    /**
     * Reply to an existing communication
     */
    public function reply(ReplyToCommunicationRequest $request, ProviderCommunication $providerCommunication): RedirectResponse
    {
        try {
            $this->authorize('create', ProviderCommunication::class);

            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['parent_id'] = $providerCommunication->id;
            $data['thread_id'] = $providerCommunication->thread_id;
            $data['provider_id'] = $providerCommunication->provider_id;
            $data['direction'] = 'outbound';
            $data['status'] = 'sent';

            $reply = $this->communicationService->createCommunication($data);

            Log::info('Reply sent', [
                'id' => $reply->id,
                'parent_id' => $providerCommunication->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('provider-communications.show', $providerCommunication)
                ->with('success', 'Reply sent successfully.');
        } catch (Exception $e) {
            Log::error('Failed to send reply', [
                'parent_id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error', 'Failed to send reply.');
        }
    }

    /**
     * Mark communication as read
     */
    public function markAsRead(MarkAsReadRequest $request, ProviderCommunication $providerCommunication): JsonResponse
    {
        try {
            $this->authorize('update', $providerCommunication);

            $result = $this->communicationService->markAsRead($providerCommunication);

            if ($result) {
                return response()->json(['success' => true, 'message' => 'Marked as read']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to mark as read'], 400);
        } catch (Exception $e) {
            Log::error('Failed to mark communication as read', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to mark as read'], 500);
        }
    }

    /**
     * Search communications
     */
    public function search(SearchCommunicationRequest $request): View
    {
        try {
            $query = $request->get('q');
            $communications = $this->communicationService->searchCommunications($query);

            return view('provider-communications.search', compact('communications', 'query'));
        } catch (Exception $e) {
            Log::error('Failed to search communications', [
                'query' => $request->get('q'),
                'error' => $e->getMessage()
            ]);

            return view('provider-communications.search')->with('error', 'Search failed.');
        }
    }

    /**
     * Get communications by provider
     */
    public function byProvider(int $providerId): View
    {
        try {
            $communications = $this->communicationService->getCommunicationsByProvider($providerId);

            return view('provider-communications.by-provider', compact('communications', 'providerId'));
        } catch (Exception $e) {
            Log::error('Failed to get communications by provider', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to load communications.');
        }
    }

    /**
     * Get communications by user
     */
    public function byUser(int $userId): View
    {
        try {
            $communications = $this->communicationService->getCommunicationsByUser($userId);

            return view('provider-communications.by-user', compact('communications', 'userId'));
        } catch (Exception $e) {
            Log::error('Failed to get communications by user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to load communications.');
        }
    }

    /**
     * Show conversation between provider and user
     */
    public function conversation(int $providerId, int $userId): View
    {
        try {
            $conversation = $this->communicationService->getConversation($providerId, $userId);

            return view('provider-communications.conversation', compact('conversation', 'providerId', 'userId'));
        } catch (Exception $e) {
            Log::error('Failed to get conversation', [
                'provider_id' => $providerId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to load conversation.');
        }
    }

    /**
     * Show communication thread
     */
    public function thread(string $threadId): View
    {
        try {
            $thread = $this->communicationService->getThread($threadId);

            return view('provider-communications.thread', compact('thread', 'threadId'));
        } catch (Exception $e) {
            Log::error('Failed to get thread', [
                'thread_id' => $threadId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to load thread.');
        }
    }

    /**
     * Archive communication
     */
    public function archive(ProviderCommunication $providerCommunication): RedirectResponse
    {
        try {
            $this->authorize('update', $providerCommunication);

            $result = $this->communicationService->archiveCommunication($providerCommunication);

            if ($result) {
                return back()->with('success', 'Communication archived successfully.');
            }

            return back()->with('error', 'Failed to archive communication.');
        } catch (Exception $e) {
            Log::error('Failed to archive communication', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to archive communication.');
        }
    }

    /**
     * Unarchive communication
     */
    public function unarchive(ProviderCommunication $providerCommunication): RedirectResponse
    {
        try {
            $this->authorize('update', $providerCommunication);

            $result = $this->communicationService->unarchiveCommunication($providerCommunication);

            if ($result) {
                return back()->with('success', 'Communication unarchived successfully.');
            }

            return back()->with('error', 'Failed to unarchive communication.');
        } catch (Exception $e) {
            Log::error('Failed to unarchive communication', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to unarchive communication.');
        }
    }

    /**
     * Set communication as urgent
     */
    public function setUrgent(ProviderCommunication $providerCommunication): RedirectResponse
    {
        try {
            $this->authorize('update', $providerCommunication);

            $result = $this->communicationService->setUrgent($providerCommunication);

            if ($result) {
                return back()->with('success', 'Communication marked as urgent.');
            }

            return back()->with('error', 'Failed to mark communication as urgent.');
        } catch (Exception $e) {
            Log::error('Failed to set communication as urgent', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to mark communication as urgent.');
        }
    }

    /**
     * Unset communication as urgent
     */
    public function unsetUrgent(ProviderCommunication $providerCommunication): RedirectResponse
    {
        try {
            $this->authorize('update', $providerCommunication);

            $result = $this->communicationService->unsetUrgent($providerCommunication);

            if ($result) {
                return back()->with('success', 'Communication unmarked as urgent.');
            }

            return back()->with('error', 'Failed to unmark communication as urgent.');
        } catch (Exception $e) {
            Log::error('Failed to unset communication as urgent', [
                'id' => $providerCommunication->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to unmark communication as urgent.');
        }
    }
}
