<?php

namespace App\Http\Controllers;

use App\Article;
use App\KinoEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KinoEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = KinoEvent::select('kino_events.*', DB::raw('false as expanded'))->with('article','lector','place');
        if ($request->last) $query = $query->where('start','<',$request->last);
        return $query->orderBy('start','desc')->limit(3)->get();
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
    public function show($id)
    {
        //
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
