<?php

namespace App\Enums;

enum CommunicationType: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case CHAT = 'chat';
    case SMS = 'sms';
    case VIDEO_CALL = 'video_call';
    case IN_PERSON = 'in_person';
    case SUPPORT_TICKET = 'support_ticket';
    case COMPLAINT = 'complaint';
    case INQUIRY = 'inquiry';
    case ORDER_UPDATE = 'order_update';
    case PAYMENT_NOTIFICATION = 'payment_notification';
    case QUALITY_ISSUE = 'quality_issue';
    case DELIVERY_UPDATE = 'delivery_update';
    case CONTRACT_DISCUSSION = 'contract_discussion';
    case GENERAL = 'general';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::EMAIL->value => 'Email',
            self::PHONE->value => 'Phone',
            self::CHAT->value => 'Chat',
            self::SMS->value => 'SMS',
            self::VIDEO_CALL->value => 'Video Call',
            self::IN_PERSON->value => 'In Person',
            self::SUPPORT_TICKET->value => 'Support Ticket',
            self::COMPLAINT->value => 'Complaint',
            self::INQUIRY->value => 'Inquiry',
            self::ORDER_UPDATE->value => 'Order Update',
            self::PAYMENT_NOTIFICATION->value => 'Payment Notification',
            self::QUALITY_ISSUE->value => 'Quality Issue',
            self::DELIVERY_UPDATE->value => 'Delivery Update',
            self::CONTRACT_DISCUSSION->value => 'Contract Discussion',
            self::GENERAL->value => 'General',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }
}
