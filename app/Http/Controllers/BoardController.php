<?php

namespace App\Http\Controllers;

use App\Board;
use App\BoardMember;
use App\Services\Response;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $board_id_by_member = BoardMember::where('user_id', Auth::user()->id)->pluck('board_id')->toArray();

        $boards = Board::whereIn('id', $board_id_by_member)->orWhere('creator_id', Auth::user()->id)->get();

        return Response::success($boards);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            Board::create([
                'name' => $request->name,
                'creator_id' => Auth::user()->id
            ]);

            return Response::message('create board success');
        } catch (\Exception $exception) {
            return Response::inputFailed();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $board = Board::where('id',$id)->with(['lists','members'])->first();

        $newMember = $board->members->map(function ($member) {
            return [
                'id' => $member->user->id,
                'first_name' => $member->user->first_name,
                'last_name' => $member->user->last_name,
                'initial' => strtoupper(substr($member->user->first_name, 0, 1) . substr($member->user->last_name, 0, 1)),
            ];
        });

        $board['members'] = $newMember;

        return Response::success($board);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $board = Board::find($id);
            if (!$board) return Response::inputFailed();

            $board->update([
                'name' => $request->name
            ]);

            return Response::message('update board success');
        } catch (\Exception $exception) {
            return Response::inputFailed();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $board = Board::find($id);
        $board->delete();

        return Response::message('delete board success');
    }

    private function rules()
    {
        return [
            'name' => 'required|string|max:255'
        ];
    }

    public function addMember(Request $request, $board_id)
    {
        try {
            $this->validate($request, $this->memberRules());

            $user = User::where('username', $request->username)->first();

            BoardMember::firstOrCreate([
                'board_id' => $board_id,
                'user_id' => $user->id
            ]);

            return Response::message('add member success');
        } catch (\Exception $exception) {
            return Response::inputFailed('user did not exist');
        }
    }

    public function removeMember($board_id, $member_id)
    {
        BoardMember::where([
            'board_id' => $board_id,
            'user_id' => $member_id
        ])->delete();

        return Response::message('remove member success');
    }

    private function memberRules()
    {
        return [
            'username' => 'exists:users'
        ];
    }
}
