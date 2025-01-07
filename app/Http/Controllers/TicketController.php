<?php

namespace App\Http\Controllers;

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

        return response()->json([
            'ticket' => $ticket->load([
                'client:mobile',
                'priority:id,title',
                'appChannelCategory:id,title',
            ]),
            'message' => $message,
        ], 201);
    }


    public function getTickets(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        $ticket = Ticket::with([
            'client:id,first_name,last_name',
            'messages:id,ticket_id,client_id,user_id,description,created_at',
            'status:id,title',
            'priority:id,title',
            'appChannel:id,title',
            'category:id,title',
        ])->find($validated['ticket_id']);

        /*$ticket = [
            'id' => 1,
            'client' => [
                'id' => 123,
                'first_name' => 'Ali',
                'last_name' => 'Ahmadi',
            ],
            'messages' => [
                [
                    'id' => 1,
                    'ticket_id' => 1,
                    'client_id' => 123,
                    'user_id' => null,
                    'description' => 'This is a client message.',
                    'created_at' => '2025-01-06T14:00:00.000000Z',
                ],
                [
                    'id' => 2,
                    'ticket_id' => 1,
                    'client_id' => null,
                    'user_id' => 456,
                    'description' => 'This is an expert response.',
                    'created_at' => '2025-01-06T14:05:00.000000Z',
                ],
            ],
            'status' => [
                'id' => 1,
                'title' => 'Open',
            ],
            'priority' => [
                'id' => 1,
                'title' => 'High',
            ],
            'appChannel' => [
                'id' => 1,
                'title' => 'Web',
            ],
            'category' => [
                'id' => 1,
                'title' => 'Technical Support',
            ],
            'created_at' => '2025-01-06T13:00:00.000000Z',
            'updated_at' => '2025-01-06T13:05:00.000000Z',
        ];*/

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }
}
