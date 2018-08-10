<?php

namespace App\Http\Controllers;

use App\Article;
use App\KinoEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KinoEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function check_seats($id) {
        $kinoEvent = KinoEvent::find($id);
        $seans = substr($kinoEvent->web,strripos($kinoEvent->web,'/'));
        $ch = curl_init("https://kinokassa.kinoplan24.ru/api/v2/seance" . $seans);
        $header = array();
        $header[] = "Referer: " . $kinoEvent->web;
        $header[] =	"Origin: https://kinowidget.kinoplan.ru";
        $header[] = "Content-type: application/json";
        $header[] = "X-Application-Token: 51859cc5bb6556ae4cdc19e92b5ff834";
        $header[] = "X-Platform: widget";
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_MAXREDIRS,99);
        $s=curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (strlen($err)>0) {
            return false;
        }
        $seats = json_decode($s)->seats;
        $kinoEvent->seat_count = count($seats);
        $kinoEvent->seat_free = count($seats);;
        foreach ($seats as $seat)
            if (!$seat->is_available) $kinoEvent->seat_free--;
        $kinoEvent->updated_at = Carbon::now();
        $kinoEvent->save();
        return true;
    }

    public function index(Request $request)
    {
        //
        $query = KinoEvent::select(
            'kino_events.*',
            DB::raw('false as expanded'),
            DB::raw('kino_events.end<now() as first'),
            DB::raw('abs(unix_timestamp(kino_events.end)-unix_timestamp(now())) as second')
        )->with('article','lector','place');
        $skip=0;
        if ($request->length) $skip=$request->length;
        return $query->orderBy('first')->orderBy('second')->skip($skip)->limit(3)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $kinoEvent = new KinoEvent;
        $article = new Article;
        $article->name = $request->article['name'];
        $article->short_text = $request->article['short_text'];
        $article->content = $request->article['content'];
        $article->save();
        $kinoEvent->article_id = $article->id;
        $kinoEvent->lector_id = $request->lector['id'];
        $kinoEvent->place_id = $request->place['id'];
        $kinoEvent->start = $request->start;
        $kinoEvent->end = $request->end;
        $kinoEvent->web = $request->web;
        $kinoEvent->save();
        return response()->json(['message'=>'ok','id' => $kinoEvent->id,'article_id'=>$article->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
        if ($request->check_seats) {
            $this->check_seats($id);
        }
        return KinoEvent::with(['article','lector','place'])->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'article.name' => 'required|string|max:255',
            'article.short_text' => 'required|string|max:255',
            'id' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date',
            'lector.id' => 'required|integer|gt:0',
            'place.id' => 'required|integer|gt:0',
        ]);
        if ($request->id == 0 ) return $this->store($request);
        $this->validate($request, [
            'article.id' => 'required|integer|gt:0',
        ]);
        $kinoEvent = KinoEvent::find($id);
        $article = Article::find($kinoEvent->article_id);
        $article->name = $request->article['name'];
        $article->short_text = $request->article['short_text'];
        $article->content = $request->article['content'];
        $article->save();
        $kinoEvent->lector_id = $request->lector['id'];
        $kinoEvent->place_id = $request->place['id'];
        $kinoEvent->start = $request->start;
        $kinoEvent->end = $request->end;
        $kinoEvent->web = $request->web;
        $kinoEvent->mob_small = $request->mob_small;
        $kinoEvent->mob_big = $request->mob_big;
        $kinoEvent->dtp_small = $request->dtp_small;
        $kinoEvent->dtp_big = $request->dtp_big;
        $kinoEvent->save();
        return response()->json(['message'=>'ok','id' => $kinoEvent->id,'article_id'=>$kinoEvent->article_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
