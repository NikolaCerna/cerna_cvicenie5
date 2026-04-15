<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $this->authorize('viewAny', Note::class);

        $notes = Note::query()
            ->select(['id', 'user_id', 'title', 'body', 'status', 'is_pinned', 'created_at'])
            ->with([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ])
            ->whereIn('status', ['published', 'archived'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(5);

        return response()->json([
            'notes' => $notes,
        ], Response::HTTP_OK);
    }

    public function myNotes(Request $request)
    {
        $this->authorize('viewAny', Note::class);

        $notes = $request->user()
            ->notes()
            ->select(['id', 'user_id', 'title', 'body', 'status', 'is_pinned', 'created_at'])
            ->with([
                'categories:id,name,color',
            ])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(5);

        return response()->json([
            'notes' => $notes,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $this->authorize('create', Note::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'body'  => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_pinned' => ['sometimes', 'boolean'],

            'categories' => ['sometimes', 'array', 'max:3'],
            'categories.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);

//        $note = Note::create([
//            'user_id' => $request->user()->id,
//            'title'     => $validated['title'],
//            'body'      => $validated['body'] ?? null,
//            'status'    => $validated['status'] ?? 'draft',
//            'is_pinned' => $validated['is_pinned'] ?? false,
//        ]);

        // alebo lepšie riešenie, len potom odstráňte z fillable user_id...
        $note = $request->user()->notes()->create([
            'title'     => $validated['title'],
            'body'      => $validated['body'] ?? null,
            'status'    => $validated['status'] ?? 'draft',
            'is_pinned' => $validated['is_pinned'] ?? false,
        ]);

        if (!empty($validated['categories'])) {
            $note->categories()->sync($validated['categories']);
        }

        return response()->json([
            'message' => 'Poznámka bola úspešne vytvorená.',
            'note' => $note->load([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ]),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */

    public function show(Note $note)
    {
        $this->authorize('view', $note);

        return response()->json([
            'note' => $note
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_pinned' => ['sometimes', 'boolean'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);

        $note->update($validated);

        if (array_key_exists('categories', $validated)) {
            $note->categories()->sync($validated['categories']);
        }

        return response()->json([
            'message' => 'Poznámka bola aktualizovaná.',
            'note' => $note->load([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ]),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Note $note)
    {

        $this->authorize('delete', $note);

        $note->delete();

        return response()->json(['message' => 'Poznámka bola úspešne odstránená.'], Response::HTTP_OK);
    }


    // vlastné metódy - QB
    public function statsByStatus()
    {
        $this->authorize('viewStats', Note::class);
        $stats = Note::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return response()->json(['stats' => $stats], Response::HTTP_OK);
    }

    public function archiveOldDrafts()
    {
        $this->authorize('archiveDrafts', Note::class);
        $notes = Note::where('status', 'draft')
            ->where('updated_at', '<', now()->subDays(30))
            ->get();

        $count = 0;
        foreach ($notes as $note) {
            $note->archive();
            $count++;
        }

        return response()->json([
            'message' => 'Staré koncepty boli archivované.',
            'affected_rows' => $count,
        ]);
    }

    public function userNotesWithCategories(string $userId)
    {
        $this->authorize('viewUserNotes', [Note::class, $userId]);

        $notes = Note::where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json(['notes' => $notes], Response::HTTP_OK);
    }

    public function publish(Note $note) {
        $this->authorize('update', $note);
        $note->publish();
        return response()->json(
            ['note' => $note], Response::HTTP_OK);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Note::class);
        $q = trim((string) $request->query('q', ''));

        $notes = Note::searchPublished($q);

        return response()->json(['query' => $q, 'notes' => $notes], Response::HTTP_OK);
    }

    public function pin(Note $note)
    {
        $this->authorize('update', $note);

        $note->pin();

        return response()->json(['note' => $note], Response::HTTP_OK);
    }

    public function unpin(Note $note)
    {
        $this->authorize('update', $note);

        $note->unpin();

        return response()->json(['note' => $note], Response::HTTP_OK);
    }

    public function pinnedNotes()
    {
        $this->authorize('viewAny', Note::class);
        $notes = Note::where('is_pinned', true)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json(['notes' => $notes], Response::HTTP_OK);
    }


}
