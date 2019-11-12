<?php

namespace App\Http\Controllers;

use App\Board;
use App\BoardList;
use App\Services\Response;
use Illuminate\Http\Request;

class BoardListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $board_id)
    {
        try {
            $this->validate($request, $this->rules());

            $order = BoardList::where('board_id', $board_id)->orderBy('order', 'DESC')->first()->order ?? 0;

            BoardList::create([
                'name' => $request->name,
                'board_id' => $board_id,
                'order' => $order + 1
            ]);

            return Response::message('create list success');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $board_id, $list_id)
    {
        try {
            $this->validate($request, $this->rules());

            $list = BoardList::find($list_id);
            $list->name = $request->name;
            $list->save();

            return Response::message('update list success');
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
    public function destroy($board_id, $list_id)
    {
        $list = BoardList::find($list_id);
        $list->delete();

        return Response::message('delete list success');
    }

    private function rules()
    {
        return [
            'name' => 'required|string|max:255'
        ];
    }

    public function moveRight(Request $request, $board_id, $list_id)
    {
        $list = BoardList::find($list_id);

        $nextList = BoardList::where([
            'board_id' => $board_id,
            ['order', '>', $list->order]
        ])
            ->orderBy('order', 'ASC')
            ->first();

        if ($list && $nextList) {
            $first = $list->order;
            $second = $nextList->order;

            $list->order = $second;
            $nextList->order = $first;
            $list->save();
            $nextList->save();
        }

        return Response::message('move success');
    }

    public function moveLeft(Request $request, $board_id, $list_id)
    {
        $list = BoardList::find($list_id);

        $nextList = BoardList::where([
            'board_id' => $board_id,
            ['order', '<', $list->order]
        ])
            ->orderBy('order', 'DESC')
            ->first();

        if ($list && $nextList) {
            $first = $list->order;
            $second = $nextList->order;

            $list->order = $second;
            $nextList->order = $first;
            $list->save();
            $nextList->save();
        }

        return Response::message('move success');
    }
}
