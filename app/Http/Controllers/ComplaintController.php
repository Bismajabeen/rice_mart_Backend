<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\NotificationService;

class ComplaintController extends Controller
{
    // POST /api/complaints — customer or seller creates a complaint
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = $this->resolveComplainantRole($user);

        $validated = $request->validate([
            'category'   => 'required|in:payment,order,account,other',
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        $complaint = Complaint::create([
            'user_id'  => $user->id,
            'role'     => $role,
            'category' => $validated['category'],
            'subject'  => $validated['subject'],
            'status'   => 'open',
        ]);

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('complaints', 'public')
            : null;

        $complaint->messages()->create([
            'sender_id'       => $user->id,
            'sender_role'     => 'complainant',
            'message'         => $validated['message'],
            'attachment_path' => $attachmentPath,
        ]);

        // =========================
        // NOTIFY ADMINS — new complaint filed
        // recipient_role: 'admin' tells the client which detail screen
        // to open, independent of how the client stores its own role.
        // =========================
        NotificationService::sendToAdmins(
            'complaint',
            'New complaint filed',
            $user->name . ' filed a complaint: ' . $validated['subject'],
            ['complaint_id' => $complaint->id, 'recipient_role' => 'admin']
        );

        return response()->json($complaint->load('messages'), 201);
    }

    // GET /api/complaints/my — customer/seller's own complaints
    public function myComplaints(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->resolveComplainantRole($user); // just to enforce permission check

        $complaints = Complaint::where('user_id', $user->id)->latest()->get();

        return response()->json($complaints);
    }

    // GET /api/complaints — Super Admin only
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('view complaints'), 403, 'Forbidden');

        $query = Complaint::with('user:id,name,email')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json($query->get());
    }

    // GET /api/complaints/{id} — owner, or Super Admin
    public function show(Request $request, Complaint $complaint): JsonResponse
    {
        $user = $request->user();

        if ($complaint->user_id !== $user->id && !$user->can('view complaints')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($complaint->load('user:id,name,email', 'messages.sender:id,name'));
    }

    // POST /api/complaints/{id}/messages — owner replies, or Super Admin replies
    public function addMessage(Request $request, Complaint $complaint): JsonResponse
    {
        $user = $request->user();
        $isOwner = $complaint->user_id === $user->id;
        $isSuperAdmin = $user->can('manage complaints');

        if (!$isOwner && !$isSuperAdmin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'message'    => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('complaints', 'public')
            : null;

        $message = $complaint->messages()->create([
            'sender_id'       => $user->id,
            'sender_role'     => $isSuperAdmin ? 'super_admin' : 'complainant',
            'message'         => $validated['message'],
            'attachment_path' => $attachmentPath,
        ]);

        if ($isSuperAdmin && $complaint->status === 'open') {
            $complaint->update(['status' => 'in_progress']);
        }

        // =========================
        // NOTIFY THE OTHER SIDE
        // Complainant replied -> notify admins (recipient_role: 'admin').
        // Admin replied -> notify the complaint owner (recipient_role:
        // the owner's own role — 'customer' or 'seller' — taken from
        // $complaint->role, so it's never guessed on the client).
        // =========================
        if ($isSuperAdmin) {
            NotificationService::send(
                $complaint->user,
                'complaint',
                'Reply to your complaint',
                'Admin replied to your complaint: ' . $complaint->subject,
                ['complaint_id' => $complaint->id, 'recipient_role' => $complaint->role]
            );
        } else {
            NotificationService::sendToAdmins(
                'complaint',
                'New reply on complaint',
                $user->name . ' replied on complaint: ' . $complaint->subject,
                ['complaint_id' => $complaint->id, 'recipient_role' => 'admin']
            );
        }

        return response()->json($message, 201);
    }

    // PATCH /api/complaints/{id}/status — Super Admin only
    public function updateStatus(Request $request, Complaint $complaint): JsonResponse
    {
        abort_unless($request->user()->can('manage complaints'), 403, 'Forbidden');

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved',
        ]);

        $complaint->update(['status' => $validated['status']]);

        // =========================
        // NOTIFY COMPLAINT OWNER — status changed (e.g. resolved)
        // recipient_role taken from $complaint->role (the owner's role),
        // not from any client-side guess.
        // =========================
        NotificationService::send(
            $complaint->user,
            'complaint',
            'Complaint status updated',
            'Your complaint "' . $complaint->subject . '" is now ' . $validated['status'] . '.',
            ['complaint_id' => $complaint->id, 'recipient_role' => $complaint->role]
        );

        return response()->json($complaint);
    }

    // Only 'view customer dashboard' or 'view seller dashboard' can file/own a complaint
    private function resolveComplainantRole($user): string
    {
        if ($user->can('view seller dashboard')) {
            return 'seller';
        }

        if ($user->can('view customer dashboard')) {
            return 'customer';
        }

        abort(403, 'Forbidden');
    }
}