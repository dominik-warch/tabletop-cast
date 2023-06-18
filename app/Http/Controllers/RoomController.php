<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\UpdateRequest;
use App\Http\Requests\Room\StoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

use App\Models\Room;

class RoomController extends Controller
{
    public function index(): View
    {
        return view("rooms.index", [
            "rooms" => Room::all()
        ]);
    }

    public function show(Room $room): View
    {
        $audios = $room->audios;

        return view('rooms.show', compact('room', 'audios'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $slug = Str::slug($request->name);

        Room::create(array_merge($request->validated(), ['slug' => $slug]));

        return redirect(route("rooms.index"));
    }

    public function edit(Room $room): View
    {
        $audios = [];
        foreach ($room->audios as $audio) {
            $audios[$audio->slot] = $audio;
        }
        ksort($audios); // Sorts the audios by the slot number
        return view('rooms.edit', ['room' => $room, 'audios' => $audios]);
    }

    public function update(UpdateRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route('rooms.edit', ['room' => $room]);
    }

    public function destroy(Request $request, Room $room): RedirectResponse
    {
        // First delete audio files
        foreach ($room->audios as $audio) {
            Storage::disk('public')->delete($audio->file);
        }
        
        $room->delete();

        return redirect()->route('rooms.index');
    }
}
