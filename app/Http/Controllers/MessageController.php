<?php

namespace App\Http\Controllers;

use App\Models\AppChannel;
use App\Models\Client;
use App\Models\File;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;

class MessageController extends Controller
{


    public function addMessage(Request $request)
    {
        // استخراج app_channel_id از درخواست (که در میدل‌ویر اضافه شده)
        $app_channel_id = $request->get('app_channel_id');

        // بررسی معتبر بودن app_channel_id
        if (!$app_channel_id) {
            return $this->apiResponse(null, 'app_channel_id is required', false, 400);
        }

        // پیدا کردن کانال اپلیکیشن با استفاده از app_channel_id
        $appChannel = AppChannel::find($app_channel_id);
        if (!$appChannel) {
            return $this->apiResponse(null, 'Invalid app_channel_id', false, 401);
        }

        // اعتبارسنجی ورودی‌ها
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',  // تیکت باید موجود باشد
            'client_mobile' => 'required|numeric|digits:11', // شماره موبایل باید 11 رقمی باشد
            'body' => 'required|string',  // بدنه پیام باید موجود باشد
            'attachments' => 'nullable|array',  // پیوست‌ها می‌توانند اختیاری باشند
            'attachments.*.file' => 'required|string',  // هر پیوست باید شامل داده‌های base64 باشد
        ]);

        // پیدا کردن تیکت بر اساس ticket_id
        $ticket = Ticket::where('id', $validated['ticket_id'])
                        ->where('app_channel_id', $app_channel_id)  // محدود کردن به app_channel_id
                        ->first();

        if (!$ticket) {
            return $this->apiResponse(null, 'Ticket not found in the specified app channel', false, 404);
        }

        // پیدا کردن کلاینت بر اساس اطلاعات توکن (اگر نیاز باشد)
        $client = Client::where('mobile' , $validated['client_mobile'])->first(); // فرض بر این است که کلاینت از توکن JWT شناسایی می‌شود

        if (!$client) {
            return $this->apiResponse(null, 'Client not found', false, 404);
        }

        if ($client->id !== $ticket->client_id) {
            return $this->apiResponse(null, 'This ticket does not belong to the specified client.', false, 404);
        }

        // ایجاد پیام جدید
        $message = $ticket->messages()->create([
            'client_id' => $client->id,
            'description' => $validated['body'],
        ]);

        // اضافه کردن پیوست‌ها اگر وجود داشته باشند
        if (isset($validated['attachments']) && count($validated['attachments']) > 0) {
            foreach ($validated['attachments'] as $attachment) {
                // ایجاد فایل از داده‌های Base64
                $fileData = $attachment['file'];

                // ذخیره فایل (این می‌تواند به هر روشی که شما برای ذخیره‌سازی فایل‌ها پیاده‌سازی کرده‌اید انجام شود)
                $file = File::create(['file' => $fileData]);

                // اضافه کردن پیوست به پیام
                $message->attachments()->create(['file_id' => $file->id]);
            }
        }

        // ساختار پاسخ به صورت apiResponse
        $response = [
            'ticket_id' => $ticket->id,
            'client_mobile' => $client->mobile,
            'message_body' => $message->description,
            'attachments' => $message->attachments->map(function ($attachment) {
                return $attachment->file->file;  // بازگشت داده فایل به صورت base64
            }),
            'created_at' => $message->created_at->toISOString(),
        ];

        // بازگشت داده‌ها
        return $this->apiResponse($response, 'Message added successfully.', true, 200);

    }

}
