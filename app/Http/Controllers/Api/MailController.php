<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $user = auth()->user();

        if (Gate::denies('super-admin', $user)) {
            return response()->json([
                'error' => "You do not have permission to views a user."
            ], 403);
        }

        $request->validate([
            'to_email'      => ['required', 'email'],
            'title'         => ['sometimes', 'string'],
            'text'          => ['sometimes', 'string'],
            'attachments.*' => ['sometimes', 'file', 'file|mimes:pdf,docx,doc,jpg,jpeg,png,gif'],
        ]);


        $toSend      = $request->to_email;
        $title       = $request->title;
        $text        = $request->text;
        $attachments = $request->file('attachments');

        Email::create([
            'title'       => $title,
            'text'        => $text,
            'attachments' => json_encode($attachments) // Преобразуйте прикрепленные файлы в JSON
        ]);

        $mail = new TestMail($title, $text);

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $mail->attach($attachment);
            }
        }

        Mail::to($toSend)->queue($mail);

        return response()->json([
            'message' => 'Email sent successfully.'
        ], 200);
    }
}
