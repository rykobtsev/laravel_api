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

    /**
     * @OA\Post(
     *     path="/api/email",
     *     summary="Send Email",
     *     operationId="sendEmail",
     *     tags={"Email"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Send email data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="to_email", type="string", format="email", example="test_user@test.com", description="User email"),
     *                 @OA\Property(property="title", type="string", example="Title email"),
     *                 @OA\Property(property="text", type="string", example="Content letter email"),
     *                 @OA\Property(
     *                     property="attachments",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Array of file attachments to email. Supports file types: pdf, docx, doc, jpg, jpeg, png, gif"
     *                     )
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="field", type="string"),
     *             @OA\Property(property="message", type="string"),
     *         )
     *     )
     * )
     */
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

        $savedAttachmentPaths = [];

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $path                   = $attachment->store('email_attachments');
                $savedAttachmentPaths[] = $path;
            }
        }

        Email::create([
            'title'       => $title,
            'text'        => $text,
            'attachments' => json_encode($savedAttachmentPaths)
        ]);

        $mail = new TestMail($title, $text);

        if ($savedAttachmentPaths) {
            foreach ($savedAttachmentPaths as $path) {
                $mail->attach(storage_path('app/' . $path));
            }
        }

        Mail::to($toSend)->queue($mail);

        return response()->json([
            'message' => 'Email sent successfully.'
        ], 200);
    }
}