<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /** GET /v1/forum/posts?formation_id=... */
    public function index(Request $request): JsonResponse
    {
        $query = ForumPost::with('user')->withCount('replies');

        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->input('formation_id'));
        }

        $posts = $query->orderByDesc('is_pinned')->latest()->get();

        return response()->json(['success' => true, 'data' => $posts]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $data['user_id'] = $request->user()->id;

        $post = ForumPost::create($data);

        return response()->json(['success' => true, 'message' => 'Post créé', 'data' => $post->load('user')], 201);
    }

    public function show(ForumPost $post): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $post->load(['user', 'replies.user'])]);
    }

    public function destroy(Request $request, ForumPost $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }

        $post->delete();

        return response()->json(['success' => true, 'message' => 'Post supprimé']);
    }

    /** POST /v1/forum/posts/{post}/replies */
    public function storeReply(Request $request, ForumPost $post): JsonResponse
    {
        $data = $request->validate(['content' => 'required|string']);
        $data['post_id'] = $post->id;
        $data['user_id'] = $request->user()->id;

        $reply = ForumReply::create($data);

        return response()->json(['success' => true, 'message' => 'Réponse ajoutée', 'data' => $reply->load('user')], 201);
    }

    public function destroyReply(Request $request, ForumReply $reply): JsonResponse
    {
        if ($reply->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }

        $reply->delete();

        return response()->json(['success' => true, 'message' => 'Réponse supprimée']);
    }

    /** POST /v1/forum/posts/{post}/like — toggle (like si absent, unlike si déjà liké) */
    public function toggleLikePost(Request $request, ForumPost $post): JsonResponse
    {
        return $this->toggleLike($request, $post);
    }

    /** POST /v1/forum/replies/{reply}/like */
    public function toggleLikeReply(Request $request, ForumReply $reply): JsonResponse
    {
        return $this->toggleLike($request, $reply);
    }

    private function toggleLike(Request $request, $likeable): JsonResponse
    {
        $existing = $likeable->likes()->where('user_id', $request->user()->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $likeable->likes()->create(['user_id' => $request->user()->id]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'data' => ['liked' => $liked, 'like_count' => $likeable->likes()->count()],
        ]);
    }
}
