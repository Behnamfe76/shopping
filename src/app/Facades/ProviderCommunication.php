<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\ProviderCommunicationService;

/**
 * @method static \Illuminate\Database\Eloquent\Collection getAllCommunications()
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginateCommunications(int $perPage = 15)
 * @method static \App\Models\ProviderCommunication|null findCommunication(int $id)
 * @method static \App\DTOs\ProviderCommunicationDTO|null findCommunicationDTO(int $id)
 * @method static \App\Models\ProviderCommunication createCommunication(array $data)
 * @method static \App\DTOs\ProviderCommunicationDTO createCommunicationDTO(array $data)
 * @method static bool updateCommunication(\App\Models\ProviderCommunication $communication, array $data)
 * @method static \App\DTOs\ProviderCommunicationDTO|null updateCommunicationDTO(\App\Models\ProviderCommunication $communication, array $data)
 * @method static bool deleteCommunication(\App\Models\ProviderCommunication $communication)
 * @method static \Illuminate\Database\Eloquent\Collection getCommunicationsByProvider(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getCommunicationsByProviderDTO(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getCommunicationsByUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection getCommunicationsByUserDTO(int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection searchCommunications(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchCommunicationsDTO(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection getUrgentCommunications(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getUnreadCommunications(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getUnrepliedCommunications(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getConversation(int $providerId, int $userId, int $limit = 50)
 * @method static \Illuminate\Database\Eloquent\Collection getThread(string $threadId)
 * @method static int getCommunicationCount(int $providerId)
 * @method static int getUnreadCount(int $providerId)
 * @method static int getUrgentCount(int $providerId)
 * @method static bool markAsRead(\App\Models\ProviderCommunication $communication)
 * @method static bool markAsReplied(\App\Models\ProviderCommunication $communication)
 * @method static bool markAsClosed(\App\Models\ProviderCommunication $communication)
 * @method static bool archiveCommunication(\App\Models\ProviderCommunication $communication)
 * @method static bool unarchiveCommunication(\App\Models\ProviderCommunication $communication)
 * @method static bool setUrgent(\App\Models\ProviderCommunication $communication)
 * @method static bool unsetUrgent(\App\Models\ProviderCommunication $communication)
 * @method static \App\Models\ProviderCommunication sendCommunicationToProvider(int $providerId, array $data)
 * @method static \App\Models\ProviderCommunication sendCommunicationToUser(int $userId, array $data)
 * @method static \App\Models\ProviderCommunication replyToCommunication(\App\Models\ProviderCommunication $parentCommunication, array $data)
 * @method static \Illuminate\Database\Eloquent\Collection bulkCreateCommunications(array $communicationsData)
 * @method static array bulkUpdateCommunications(array $communicationsData)
 * @method static array bulkDeleteCommunications(array $communicationIds)
 * @method static \App\Services\ProviderCommunicationService getService()
 *
 * @see \App\Services\ProviderCommunicationService
 */
class ProviderCommunication extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ProviderCommunicationService::class;
    }

    /**
     * Get the service instance directly
     *
     * @return \App\Services\ProviderCommunicationService
     */
    public static function getService()
    {
        return app(ProviderCommunicationService::class);
    }

    /**
     * Send a quick message to a provider
     *
     * @param int $providerId
     * @param string $subject
     * @param string $message
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function quickMessage(int $providerId, string $subject, string $message, array $additionalData = [])
    {
        $data = array_merge([
            'provider_id' => $providerId,
            'subject' => $subject,
            'message' => $message,
            'communication_type' => 'general',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'normal',
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Send a quick message to a user
     *
     * @param int $userId
     * @param string $subject
     * @param string $message
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function quickMessageToUser(int $userId, string $subject, string $message, array $additionalData = [])
    {
        $data = array_merge([
            'user_id' => $userId,
            'subject' => $subject,
            'message' => $message,
            'communication_type' => 'general',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'normal',
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Send an urgent message to a provider
     *
     * @param int $providerId
     * @param string $subject
     * @param string $message
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function urgentMessage(int $providerId, string $subject, string $message, array $additionalData = [])
    {
        $data = array_merge([
            'provider_id' => $providerId,
            'subject' => $subject,
            'message' => $message,
            'communication_type' => 'general',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'urgent',
            'is_urgent' => true,
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Send a support ticket
     *
     * @param int $providerId
     * @param string $subject
     * @param string $message
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function supportTicket(int $providerId, string $subject, string $message, array $additionalData = [])
    {
        $data = array_merge([
            'provider_id' => $providerId,
            'subject' => $subject,
            'message' => $message,
            'communication_type' => 'support_ticket',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'normal',
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Send an order update notification
     *
     * @param int $providerId
     * @param string $orderNumber
     * @param string $updateMessage
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function orderUpdate(int $providerId, string $orderNumber, string $updateMessage, array $additionalData = [])
    {
        $data = array_merge([
            'provider_id' => $providerId,
            'subject' => "Order Update: {$orderNumber}",
            'message' => $updateMessage,
            'communication_type' => 'order_update',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'normal',
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Send a payment notification
     *
     * @param int $providerId
     * @param string $paymentReference
     * @param string $paymentMessage
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function paymentNotification(int $providerId, string $paymentReference, string $paymentMessage, array $additionalData = [])
    {
        $data = array_merge([
            'provider_id' => $providerId,
            'subject' => "Payment Notification: {$paymentReference}",
            'message' => $paymentMessage,
            'communication_type' => 'payment_notification',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'normal',
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Send a delivery update
     *
     * @param int $providerId
     * @param string $deliveryReference
     * @param string $deliveryMessage
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function deliveryUpdate(int $providerId, string $deliveryReference, string $deliveryMessage, array $additionalData = [])
    {
        $data = array_merge([
            'provider_id' => $providerId,
            'subject' => "Delivery Update: {$deliveryReference}",
            'message' => $deliveryMessage,
            'communication_type' => 'delivery_update',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'normal',
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Send a quality issue notification
     *
     * @param int $providerId
     * @param string $issueDescription
     * @param string $issueDetails
     * @param array $additionalData
     * @return \App\Models\ProviderCommunication
     */
    public static function qualityIssue(int $providerId, string $issueDescription, string $issueDetails, array $additionalData = [])
    {
        $data = array_merge([
            'provider_id' => $providerId,
            'subject' => "Quality Issue: {$issueDescription}",
            'message' => $issueDetails,
            'communication_type' => 'quality_issue',
            'direction' => 'outbound',
            'status' => 'sent',
            'priority' => 'high',
            'is_urgent' => true,
        ], $additionalData);

        return static::createCommunication($data);
    }

    /**
     * Get communication statistics for a provider
     *
     * @param int $providerId
     * @return array
     */
    public static function getProviderStats(int $providerId): array
    {
        return [
            'total' => static::getCommunicationCount($providerId),
            'unread' => static::getUnreadCount($providerId),
            'urgent' => static::getUrgentCount($providerId),
        ];
    }

    /**
     * Check if provider has unread communications
     *
     * @param int $providerId
     * @return bool
     */
    public static function hasUnread(int $providerId): bool
    {
        return static::getUnreadCount($providerId) > 0;
    }

    /**
     * Check if provider has urgent communications
     *
     * @param int $providerId
     * @return bool
     */
    public static function hasUrgent(int $providerId): bool
    {
        return static::getUrgentCount($providerId) > 0;
    }

    /**
     * Get recent communications for a provider
     *
     * @param int $providerId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentForProvider(int $providerId, int $limit = 5)
    {
        return static::getCommunicationsByProvider($providerId)->take($limit);
    }

    /**
     * Get recent communications for a user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentForUser(int $userId, int $limit = 5)
    {
        return static::getCommunicationsByUser($userId)->take($limit);
    }
}
