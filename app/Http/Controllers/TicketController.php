<?php

namespace App\Http\Controllers;

use App\Models\AppChannel;
use App\Models\Client;
use App\Models\File;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $validated = $request->validate([
            'client_mobile' => 'required|numeric|digits:11', // شماره موبایل باید 11 رقمی باشد
            'priority_id' => 'required|exists:priorities,id',
            'category_id' => 'required|exists:app_channel_categories,id',
            'title' => 'required|string|max:255',
            'message_body' => 'required|string',
            'message_attachments' => 'nullable|array', // فایل‌ها اختیاری هستند
            'message_attachments.*.file' => 'required|string', // هر فایل باید به صورت رشته Base64 باشد
        ]);

        // پیدا کردن یا ایجاد Client بر اساس شماره موبایل
        $client = Client::firstOrCreate(
            ['mobile' => $validated['client_mobile']], // شرایط جستجو
            ['mobile' => $validated['client_mobile'] , 'first_name' => 'no name', 'last_name' => 'no name']  // اطلاعاتی که باید هنگام ایجاد وارد شود
        );

        // ایجاد تیکت جدید
        $ticket = Ticket::create([
            'client_id' => $client->id,
            'ticket_status_id' => 1,  // فرض بر اینکه وضعیت تیکت پیش‌فرض 1 است
            'priority_id' => $validated['priority_id'],
            'app_channel_id' => $validated['category_id'],  // فرض بر اینکه این اشتباه نباشد، یا آن را تنظیم کنید
            'app_channel_category_id' => $validated['category_id'],
            'title' => $validated['title'],
        ]);

        // ایجاد پیام برای تیکت
        $message = $ticket->messages()->create([
            'client_id' => $client->id,
            'description' => $validated['message_body'],
        ]);

        // اضافه کردن پیوست‌ها اگر وجود داشته باشند
        if (isset($validated['message_attachments'])) {
            foreach ($validated['message_attachments'] as $attachment) {
                // ایجاد فایل از داده‌های Base64
                $fileData = $attachment['file'];

                $file = File::create(['file' => $fileData]);

                // اضافه کردن پیوست به پیام
                $message->attachments()->create(['file_id' => $file->id]);
            }
        }

        $messages = $ticket->messages->map(function ($message) {
            $sender = null;
            if ($message->user_id) {
                $sender_type = 'Support'; // اگر پشتیبانی پیام را ارسال کرده باشد
                $sender_name = $message->user->name ?? 'Support'; // اگر پشتیبانی پیام را ارسال کرده باشد
            } elseif ($message->client_id) {
                $sender_type = 'Client'; // اگر پشتیبانی پیام را ارسال کرده باشد
                $sender_name = $message->client->mobile ?? 'Client'; // اگر مشتری پیام را ارسال کرده باشد
            }

            return [
                'body' => $message->description,
                'attachments' => $message->attachments->pluck('file.file'), // فایل‌های پیوست
                'sender_type' => $sender_type,
                'sender_name' => $sender_name,
                'created_at' => $message->created_at->toISOString(),
            ];
        });

        return $this->apiResponse(
            [
                'ticket' => [
                    'ticket_id' => $ticket->id,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'ticket_status_title' => $ticket->ticketStatus->title,
                    'priority_id' => $ticket->priority_id,
                    'priority_title' => $ticket->priority->title,
                    'category_id' => $ticket->app_channel_category_id,
                    'category_title' => $ticket->appChannelCategory->title,
                    'title' => $ticket->title,
                    'created_at' => $ticket->created_at->toISOString(),
                    'client_mobile' => $ticket->client->mobile,
                ],
                'messages' => $messages,
            ],
            'Ticket and message created successfully.',
            true,
            201
        );
    }


    public function lists(Request $request)
    {
        // دریافت app_channel_id از میدل‌ویر
        $appChannelId = $request->get('app_channel_id'); // فرض بر این است که در میدل‌ویر این مقدار به درخواست افزوده شده است

        // اعتبارسنجی ورودی‌ها
        $validated = $request->validate([
            'client_mobile' => 'required|numeric|digits:11', // شماره موبایل کلاینت
        ]);

        // پیدا کردن کلاینت بر اساس شماره موبایل
        $client = Client::where('mobile', $validated['client_mobile'])->first();

        if (!$client) {
            return $this->apiResponse(null, 'Client not found', false, 404);
        }

        // دریافت لیست تیکت‌ها برای این کلاینت و app_channel خاص
        $tickets = Ticket::where('client_id', $client->id)
            ->where('app_channel_id', $appChannelId)  // استفاده از app_channel_id که در میدل‌ویر استخراج شده است
            ->get();

        if ($tickets->isEmpty()) {
            return $this->apiResponse(null, 'No tickets found for this client and app channel', false, 404);
        }

        // ساختار داده‌ها برای هر تیکت
        $ticketList = $tickets->map(function ($ticket) {
            return [
                'ticket_id' => $ticket->id,
                'ticket_status_id' => $ticket->ticket_status_id,
                'ticket_status_title' => $ticket->ticketStatus->title,
                'priority_id' => $ticket->priority_id,
                'priority_title' => $ticket->priority->title,
                'category_id' => $ticket->app_channel_category_id,
                'category_title' => $ticket->appChannelCategory->title,
                'title' => $ticket->title,
                'created_at' => $ticket->created_at->toISOString(),
                'client_mobile' => $ticket->client->mobile,
            ];
        });

        // بازگشت داده‌ها با فرمت apiResponse
        return $this->apiResponse(
            ['tickets' => $ticketList],
            'Tickets retrieved successfully.',
            true,
            200
        );
    }

    public function details(Request $request)
    {
        // استخراج app_channel_id از درخواست (که در میدل‌ویر اضافه شده)
        $app_channel_id = $request->get('app_channel_id');

        // بررسی معتبر بودن app_channel_id
        if (!$app_channel_id) {
            return $this->apiResponse(null, 'app_channel_id is required', false, 400);
        }

        // پیدا کردن کانال اپلیکیشن بر اساس app_channel_id
        $appChannel = AppChannel::find($app_channel_id);
        if (!$appChannel) {
            return $this->apiResponse(null, 'Invalid app_channel_id', false, 401);
        }

        // اعتبارسنجی ورودی‌ها
        $validated = $request->validate([
            'ticket_id' => 'required',  // تیکت باید موجود باشد
            'client_mobile' => 'required|numeric|digits:11',  // شماره موبایل باید 11 رقمی باشد
        ]);

        // پیدا کردن کلاینت بر اساس شماره موبایل
        $client = Client::where('mobile', $validated['client_mobile'])->first();
        if (!$client) {
            return $this->apiResponse(null, 'Client not found', false, 404);
        }

        // پیدا کردن تیکت بر اساس ticket_id و app_channel_id
        $ticket = Ticket::with(['messages' => function ($query) {
            $query->orderBy('created_at', 'asc'); // پیام‌ها را بر اساس تاریخ مرتب می‌کند
        }])->where('id', $validated['ticket_id'])
        ->where('client_id', $client->id)
        ->where('app_channel_id', $app_channel_id)  // محدود کردن به app_channel_id
        ->first();

        if (!$ticket) {
            return $this->apiResponse(null, 'Ticket not found for this client in the specified app channel', false, 404);
        }

        // ساختار داده‌ها برای پیام‌های تیکت
        $messages = $ticket->messages->map(function ($message) {
            $sender = null;
            if ($message->user_id) {
                $sender_type = 'Support'; // اگر پشتیبانی پیام را ارسال کرده باشد
                $sender_name = $message->user->name ?? 'Support'; // اگر پشتیبانی پیام را ارسال کرده باشد
            } elseif ($message->client_id) {
                $sender_type = 'Client'; // اگر پشتیبانی پیام را ارسال کرده باشد
                $sender_name = $message->client->mobile ?? 'Client'; // اگر مشتری پیام را ارسال کرده باشد
            }

            return [
                'body' => $message->description,
                'attachments' => $message->attachments->pluck('file.file'), // فایل‌های پیوست
                'sender_type' => $sender_type,
                'sender_name' => $sender_name,
                'created_at' => $message->created_at->toISOString(),
            ];
        });


        // ساختار پاسخ
        return $this->apiResponse(
            [
                'ticket' => [
                    'ticket_id' => $ticket->id,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'ticket_status_title' => $ticket->ticketStatus->title,
                    'priority_id' => $ticket->priority_id,
                    'priority_title' => $ticket->priority->title,
                    'category_id' => $ticket->app_channel_category_id,
                    'category_title' => $ticket->appChannelCategory->title,
                    'title' => $ticket->title,
                    'created_at' => $ticket->created_at->toISOString(),
                    'client_mobile' => $ticket->client->mobile,
                ],
                'messages' => $messages,
            ],
            'Ticket details retrieved successfully.',
            true,
            200
        );
    }

}
