<?php

namespace App\Http\Middleware;

use App\Board;
use App\BoardMember;
use Closure;
use Illuminate\Support\Facades\Auth;

class MemberOnly
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $board_id = $request->segment(2);

        $board = Board::where('id', $board_id)->first();

        $member = BoardMember::where('board_id', $board_id)->pluck('user_id')->toArray();

        if ($board->creator_id == Auth::user()->id || in_array(Auth::user()->id, $member)) {
            return $next($request);
        }

        return 'error';
    }
}
