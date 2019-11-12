<?php

namespace App\Http\Controllers;

use App\BoardCard;
use App\Services\Response;
use Illuminate\Http\Request;

class BoardCardController extends Controller
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
    public function store(Request $request, $board_id, $list_id)
    {
        try {
            $order = BoardCard::where('list_id', $list_id)->orderBy('order', 'DESC')->first()->order ?? 0;

            BoardCard::create([
                'task' => $request->task,
                'order' => $order + 1,
                'list_id' => $list_id
            ]);

            return Response::message('create card success');
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
    public function update(Request $request, $board_id, $list_id, $id)
    {
        try {
            $card = BoardCard::find($id);
            $card->task = $request->task;
            $card->save();

            return Response::message('update card success');
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
    public function destroy($board_id, $list_id, $id)
    {
        $card = BoardCard::find($id);
        $card->delete();

        return Response::message('delete card success');
    }

    private function rules()
    {
        return [
            'task' => 'required|string|max:255'
        ];
    }

    public function moveUp(Request $request, $card_id)
    {
        $card = BoardCard::find($card_id);
        $nextCard = BoardCard::where([
            'list_id' => $card->list_id,
            ['order', '<', $card->order]
        ])->orderBy('order', 'DESC')->first();

        if ($card && $nextCard) {
            $first = $card->order;
            $second = $nextCard->order;

            $card->order = $second;
            $nextCard->order = $first;

            $card->save();
            $nextCard->save();
        }

        return Response::message('move success');
    }

    public function moveDown(Request $request, $card_id)
    {
        $card = BoardCard::find($card_id);
        $nextCard = BoardCard::where([
            'list_id' => $card->list_id,
            ['order', '>', $card->order]
        ])->orderBy('order', 'ASC')->first();

        if ($card && $nextCard) {
            $first = $card->order;
            $second = $nextCard->order;

            $card->order = $second;
            $nextCard->order = $first;

            $card->save();
            $nextCard->save();
        }

        return Response::message('move success');
    }
}
